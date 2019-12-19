<?php

namespace App\Controller;

use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\Survey;
use App\Form\QuestionType;
use App\Form\SurveyType;
use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/survey")
 */
class SurveyController extends AbstractController
{
    /**
     * @Route("/author/", name="survey_index", methods={"GET"})
     */
    public function index(SurveyRepository $surveyRepository): Response
    {
        return $this->render('survey/index.html.twig', [
            'surveys' => $surveyRepository->findBy(['creator' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/author/new", name="survey_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $survey = new Survey();
        $question = new Question();
        $question->setSurvey($survey);
        $answer_option = new AnswerOption();
        $answer_option->setQuestion($question);
        $question->getAnswerOptions()->add($answer_option);
        $survey->getQuestions()->add($question);

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $survey->setCreator($this->getUser());
            $entityManager->persist($survey);

            $entityManager->flush();

            return $this->redirectToRoute('survey_index');
        }

        return $this->render('survey/new.html.twig', [
            'survey' => $survey,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="survey_show", methods={"GET"})
     */
    public function show(Survey $survey): Response
    {
        return $this->render('survey/show.html.twig', [
            'survey' => $survey,
        ]);
    }

    /**
     * @Route("/author/{id}/edit", name="survey_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Survey $survey): Response
    {
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('survey_index');
        }

        return $this->render('survey/edit.html.twig', [
            'survey' => $survey,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/author/{id}", name="survey_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Survey $survey): Response
    {
        if ($this->isCsrfTokenValid('delete'.$survey->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->deleteQuestions($survey->getQuestions());
            $entityManager->remove($survey);
            $entityManager->flush();
        }

        return $this->redirectToRoute('survey_index');
    }

    private function deleteQuestions(Collection $questions){
        foreach ($questions as $question){
            foreach ($question->getAnswerOptions() as $answer){
                $this->getDoctrine()->getManager()->remove($answer);
            }
            $this->getDoctrine()->getManager()->remove($question);
        }
    }
}
