<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class AdminControllerTest extends WebTestCase
{
    public function testCreateAuthor()
    {
        $client = static::createClient();
        //Login as admin
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/new_author');
        $form = $crawler->selectButton('Save')->form();

        // set some values
        $email = 'testAuthor@testAuthor.com';
        $form['user[email]'] = $email;
        $form['user[name]'] = 'author';
        $form['user[lastName]'] = 'author';
        $form['user[birthdate][month]'] = '1';
        $form['user[birthdate][day]'] = '25';
        $form['user[birthdate][year]'] = '1988';
        $form['user[password]'] = 'password';

        // submit the form
        $crawler = $client->submit($form);

        $resposeStatus = $client->getResponse()->getStatusCode();
        $this->assertEquals(302, $resposeStatus);

        if ($resposeStatus === 302) {
            $entityManager = static::$container->get('doctrine')->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            $entityManager->remove($user);
            $entityManager->flush();
        }

        // clean up
        $this->removeTestUserFromDB($admin->getId());
    }

    public function testCreateAuthorWithInvalidEmail()
    {
        $client = static::createClient();
        //Login as admin
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/new_author');
        $form = $crawler->selectButton('Save')->form();

        // set some values
        $form['user[email]'] = 'testAuthor@a';
        $form['user[name]'] = 'author';
        $form['user[lastName]'] = 'author';
        $form['user[birthdate][month]'] = '1';
        $form['user[birthdate][day]'] = '25';
        $form['user[birthdate][year]'] = '1988';
        $form['user[password]'] = 'password';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('span.form-error-message')->count());

        // clean up
        $this->removeTestUserFromDB($admin->getId());
    }

    public function testDeleteAuthorWithGETMethod()
    {
        $client = static::createClient();
        // Prep test data
        $author = $this->createAuthor();
        $admin = $this->createAdmin();

        //Login as admin
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/'.$author->getId());

        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // clean up
        $this->removeTestUserFromDB($admin->getId());
        $this->removeTestUserFromDB($author->getId());
    }

    public function testDeleteAuthor()
    {
        $client = static::createClient();
        // Prep test data
        $author = $this->createAuthor();
        $admin = $this->createAdmin();

        //Login as admin
        $client->loginUser($admin);

        $crawler = $client->request('DELETE', '/admin/'.$author->getId());

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // clean up
        $this->removeTestUserFromDB($admin->getId());
        $this->removeTestUserFromDB($author->getId());
    }

    private function createAdmin(): User {
        $user = new User();
        $user->setEmail('testAdmin@testAdmin.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setName("Jonas");
        $user->setLastName("Jonaitis");
        $user->setBirthdate(new \DateTime());
        $user->setPassword('password');

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function createAuthor(): User {
        $user = new User();
        $user->setEmail('testAuthor@testAuthor.com');
        $user->setRoles(["ROLE_AUTHOR"]);
        $user->setName("author");
        $user->setLastName("author");
        $user->setBirthdate(new \DateTime());
        $user->setPassword('password');

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
