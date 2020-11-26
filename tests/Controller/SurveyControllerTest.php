<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\AnswerOption;
use App\Entity\AttendSurvey;
use App\Entity\Question;
use App\Entity\Survey;
use App\Entity\User;

class SurveyControllerTest extends WebTestCase
{
    public function testCreateSurvey()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $client->loginUser($author);

        $crawler = $client->request('GET', '/survey/author/new');
        $form = $crawler->selectButton('Save')->form();

        // set some values
        $title = "TEST SURVEY";
        $form['survey[title]'] = $title;
        $form['survey[questions][0][title]'] = 'TEST QUESTION';
        $form['survey[questions][0][answer_options][0][answer]'] = 'TEST ANSWER';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateSurveyNoData()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $client->loginUser($author);

        $crawler = $client->request('GET', '/survey/author/new');
        $form = $crawler->selectButton('Save')->form();

        // set some values
        $form['survey[title]'] = "";
        $form['survey[questions][0][title]'] = '';
        $form['survey[questions][0][answer_options][0][answer]'] = '';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('span.form-error-message')->count());
    }

    public function testCreateSurveyNoSurveyTitle()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $client->loginUser($author);

        $crawler = $client->request('GET', '/survey/author/new');
        $form = $crawler->selectButton('Save')->form();

        // set some values
        $form['survey[title]'] = "";
        $form['survey[questions][0][title]'] = 'QUESTION';
        $form['survey[questions][0][answer_options][0][answer]'] = 'ANSWER';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('span.form-error-message')->count());
    }

    public function testCheckSurveyResults()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $survey = $this->createTestSurvey($author);
        $client->loginUser($author);

        $crawler = $client->request('GET', '/survey/author/results/'.$survey->getId());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEraseSurveyResults()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $survey = $this->createTestSurvey($author);
        $user = $this->createUser();
        $survey = $this->addAttendSurveyToSurvey($survey, $user);
        $client->loginUser($author);


        $crawler = $client->request('DELETE', '/survey/author/results/'.$survey->getId());

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, count($survey->getAttendSurveys()));
    }

    public function testDeleteSurvey()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $survey = $this->createTestSurvey($author);
        $client->loginUser($author);

        $crawler = $client->request('DELETE', '/survey/author/'.$survey->getId());

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteSurveyWithWrongID()
    {
        $client = static::createClient();
        //Login as admin
        $author = $this->createAuthor();
        $survey = $this->createTestSurvey($author);
        $client->loginUser($author);

        $crawler = $client->request('DELETE', '/survey/author/'.($survey->getId()+\rand(0, 1000)));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
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

    private function createUser(): User {
        $user = new User();
        $user->setEmail('simpleUser@simpleUser.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setName("Simple");
        $user->setLastName("User");
        $user->setBirthdate(new \DateTime());
        $user->setPassword('password');

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function createTestSurvey(User $user): Survey {
        $question = new Question();
        $answer_option = new AnswerOption();
        $survey = new Survey();
        $survey->setTitle('Test');
        $survey->setCreator($user);
        $question-> setTitle('Test');
        $question->setSurvey($survey);
        $answer_option->setAnswer('Test');
        $answer_option->setQuestion($question);
        $question->getAnswerOptions()->add($answer_option);
        $survey->getQuestions()->add($question);


        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($survey);
        $entityManager->persist($question);
        $entityManager->persist($answer_option);
        $entityManager->flush();

        return $survey;
    }

    private function addAttendSurveyToSurvey(Survey $survey, User $user): Survey {
        $attend = new AttendSurvey();

        $attend->setUser($user);
        $attend->setSurvey($survey);

        foreach ($survey->getQuestions() as $question){
            $attend->addAnswer($question->getAnswerOptions()[0]);
        }

        $survey->addAttendSurvey($attend);

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($attend);
        $entityManager->persist($survey);
        $entityManager->flush();

        return $survey;
    }

    private function removeTestUserFromDB(int $userID) {
        $entityManager = static::$container->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->find($userID);
        $entityManager->remove($user);
        $entityManager->flush();
    }
}
