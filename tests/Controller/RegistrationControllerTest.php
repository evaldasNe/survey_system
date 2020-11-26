<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form();

        // set some values
        $email = 'test1@test1.com';
        $form['registration_form[email]'] = $email;
        $form['registration_form[name]'] = 'test';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[birthdate][month]'] = '1';
        $form['registration_form[birthdate][day]'] = '25';
        $form['registration_form[birthdate][year]'] = '1988';
        $form['registration_form[plainPassword]'] = 'password';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRegisterWithShortPassword()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form();

        // set some values
        $form['registration_form[email]'] = 'test1@test1.com';
        $form['registration_form[name]'] = 'test';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[birthdate][month]'] = '1';
        $form['registration_form[birthdate][day]'] = '25';
        $form['registration_form[birthdate][year]'] = '1988';
        $form['registration_form[plainPassword]'] = '1';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('span.form-error-message')->count());
    }

    public function testRegisterWithWrongEmailAndPassword()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form();

        // set some values
        $form['registration_form[email]'] = 'test1@.com';
        $form['registration_form[name]'] = 'test';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[birthdate][month]'] = '1';
        $form['registration_form[birthdate][day]'] = '25';
        $form['registration_form[birthdate][year]'] = '1988';
        $form['registration_form[plainPassword]'] = '1';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(1, $crawler->filter('span.form-error-message')->count());
    }
}