<?php

namespace App\Tests\Controller;

use App\Controller\AttendSurveyController;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\Survey;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AttendSurveyControllerTest extends TestCase
{
    public function testAttendsurvey()
    {
        $client = static::createClient();
        //Login in to attend the survey
        $user = $this->createTestUser();
        $creator = $this->createTestCreator();
        $client->loginUser($user);
        $question = new Question();
        $answer = new AnswerOption();
        $survey = $this->createTestSurvey($creator,$question,$answer);
        $id = $survey->getId();

        $crawler = $client->request('GET', '/user/attend/survey/'.$id);
        $form = $crawler->selectButton('Save')->form();

        // submit the form
        $crawler = $client->submit($form);

        $this->assertResponseRedirects('/');

        $this->removeTestUserFromDB($user->getId());
        $this->removeTestAnswerFromDB($answer->getId());
        $this->removeTestQuestionFromDB($question->getId());
        $this->removeTestSurveyFromDB($survey->getId());
        $this->removeTestUserFromDB($creator->getId());



    }
    private function createTestSurvey(User $user, Question $question, AnswerOption $answer): Survey {
    $survey = new Survey();
    $answer->setAnswer('Test');
    $question->setTitle('Test');
    $question->addAnswerOption($answer);
    $survey->setTitle('Test');
    $survey->setCreator($user);
    $survey->addQuestion($question);

    $entityManager = static::$container->get('doctrine')->getManager();
    $entityManager->persist($survey);
    $entityManager->persist($user);
    $entityManager->persist($question);
    $entityManager->persist($answer);
    $entityManager->flush();

    return $survey;
    }

    private function createTestUser(): User {
        $user = new User();
        $user->setEmail('test1@test1.com');
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
        $user->setEmail('test12@test12.com');
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

    private function removeTestUserFromDB(int $userID) {
        $entityManager = static::$container->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->find($userID);
        $entityManager->remove($user);
        $entityManager->flush();
    }
    private function removeTestAnswerFromDB(int $answerid)
    {
        $entityManager = static::$container->get('doctrine')->getManager();
        $answer = $entityManager->getRepository(AnswerOption::class)->find($answerid);
        $entityManager->remove($answer);
        $entityManager->flush();
    }
    private function removeTestQuestionFromDB(int $questionid)
    {
        $entityManager = static::$container->get('doctrine')->getManager();
        $question = $entityManager->getRepository(Question::class)->find($questionid);
        $entityManager->remove($question);
        $entityManager->flush();
    }

    private function removeTestSurveyFromDB(int $surveyid)
    {
        $entityManager = static::$container->get('doctrine')->getManager();
        $survey = $entityManager->getRepository(Survey::class)->find($surveyid);
        $entityManager->remove($survey);
        $entityManager->flush();
    }
}
