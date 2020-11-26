<?php

namespace App\Tests\Controller;

use App\Controller\AttendSurveyController;
use App\Entity\AnswerOption;
use App\Entity\AttendSurvey;
use App\Entity\Question;
use App\Entity\Survey;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttendSurveyControllerTest extends WebTestCase
{
    public function testAttendsurvey()
    {
        $client = static::createClient();
        //Login in to attend the survey
        $user = $this->createTestUser();
        $client->loginUser($user);
        $creator = $this->createTestCreator();
        $question = new Question();
        $answer = new AnswerOption();
        $survey = $this->createTestSurvey($creator,$question,$answer);
        $id = $survey->getId();

        $crawler = $client->request('GET', '/user/attend/survey/'.$id);
        $form = $crawler->selectButton('Save')->form();

        // submit the form
        $crawler = $client->submit($form);

        $this->assertResponseRedirects('/');
    }
    
    private function createTestSurvey(User $user, Question $question, AnswerOption $answer_option): Survey {
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
        $entityManager->persist($user);
        $entityManager->persist($question);
        $entityManager->persist($answer_option);
        $entityManager->flush();

        return $survey;
    }

    private function createTestUser(): User {
        $user = new User();
        $user->setEmail('testavimas1@test1.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setName("Jonas");
        $user->setLastName("Jonaitis");
        $user->setBirthdate(new \DateTime());
        $user->setPassword('password');

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function createTestCreator(): User {
        $user = new User();
        $user->setEmail('testavimas12@test12.com');
        $user->setRoles(["ROLE_AUTHOR"]);
        $user->setName("Jonas");
        $user->setLastName("Jonaitis");
        $user->setBirthdate(new \DateTime());
        $user->setPassword('password');

        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
