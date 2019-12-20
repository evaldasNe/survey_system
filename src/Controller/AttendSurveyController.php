<?php

namespace App\Controller;

use App\Entity\AnswerOption;
use App\Entity\AttendSurvey;
use App\Entity\Survey;
use App\Form\AttendSurveyType;
use App\Repository\AttendSurveyRepository;
use Doctrine\Common\Collections\Collection;
use http\Client\Curl\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("user/attend/survey")
 */
class AttendSurveyController extends AbstractController
{

    /**
     * @Route("/{id}", name="attend_survey", methods={"GET","POST"})
     */
    public function new(Request $request, Survey $survey, AttendSurveyRepository $attendSurveyRepository): Response
    {
        if ($attendSurveyRepository->findBy(['user' => $this->getUser(), 'survey' => $survey]) != null){
            return $this->redirectToRoute('home_page');
        }
        $attendSurvey = new AttendSurvey();
        $attendSurvey->setSurvey($survey);
        $attendSurvey->setUser($this->getUser());

        $form = $this->createFormBuilder();

        foreach ($survey->getQuestions() as $question){
            $form->add('answers'.$question->getId(), EntityType::class, [
                'class' => AnswerOption::class,
                'label' => ucfirst($question->getTitle()),
                'choice_label' => 'answer',
                'choices' => $question->getAnswerOptions(),
            ]);
        }
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            foreach ($data as $answer){
                $attendSurvey->getAnswers()->add($answer);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attendSurvey);
            $entityManager->flush();

            return $this->redirectToRoute('home_page');
        }

        return $this->render('attend_survey/new.html.twig', [
            'attend_survey' => $attendSurvey,
            'form' => $form->createView(),
            'survey' => $survey,
        ]);
    }

    /**
     * @Route("/{id}", name="attend_survey_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AttendSurvey $attendSurvey): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attendSurvey->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attendSurvey);
            $entityManager->flush();
        }

        return $this->redirectToRoute('survey_index');
    }
}
