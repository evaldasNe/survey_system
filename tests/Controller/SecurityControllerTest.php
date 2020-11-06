<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        // Prepare test data
        $password = "password";
        $user = $this->createTestUser($password);

        // set form values
        $form['email'] = $user->getEmail();
        $form['password'] = $password;
        
        // submit the form
        $crawler = $client->submit($form);

        $this->assertResponseRedirects('/');
        
        $this->removeTestUserFromDB($user->getId());
    }

    public function testLoginWithWrongPassword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        // Prepare test data
        $password = "password";
        $wrongPassword = "123456";
        $user = $this->createTestUser($password);

        // set form values
        $form['email'] = $user->getEmail();
        $form['password'] = $wrongPassword;
        
        // submit the form
        $client->followRedirects();
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $errorMessage = $crawler->filter('div.alert')->first()->text();
        $this->assertEquals("Invalid credentials.", $errorMessage);

        $this->removeTestUserFromDB($user->getId());
    }

    public function testLoginWithWrongEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        // Prepare test data
        $password = "password";
        $wrongEmail = "wrong@wrong.com";
        $user = $this->createTestUser($password);

        // set form values
        $form['email'] = $wrongEmail;
        $form['password'] = $password;
        
        // submit the form
        $client->followRedirects();
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $errorMessage = $crawler->filter('div.alert')->first()->text();
        $this->assertEquals("Email could not be found.", $errorMessage);

        $this->removeTestUserFromDB($user->getId());
    }

    private function createTestUser(string $password): User {
        $user = new User();
        $user->setEmail('test1@test1.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setName("Jonas");
        $user->setLastName("Jonaitis");
        $user->setBirthdate(new \DateTime());
        $user->setPassword(password_hash($password, 1));

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function removeTestUserFromDB(int $userID) {
        $entityManager = static::$container->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->find($userID); 
        $entityManager->remove($user);
        $entityManager->flush();
    }
}