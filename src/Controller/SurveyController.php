<?php

namespace App\Controller;

use App\Entity\AnswerOption;
use App\Entity\AttendSurvey;
use App\Entity\Question;
use App\Entity\Survey;
use App\Form\QuestionType;
use App\Form\SurveyType;
use App\Repository\AttendSurveyRepository;
use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
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

            if ($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('survey_question_add', ['id' => $survey->getId()]);
            }

            return $this->redirectToRoute('survey_index');
        }

        return $this->render('survey/new.html.twig', [
            'survey' => $survey,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/author/{id}/addQuestion", name="survey_question_add", methods={"GET","POST"})
     */
    public function addQuestion(Request $request, Survey $survey): Response
    {
        $question = new Question();
        $question->setSurvey($survey);
        $answer_option = new AnswerOption();
        $answer_option->setQuestion($question);
        $question->getAnswerOptions()->add($answer_option);
        $survey->getQuestions()->add($question);

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            if ($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('survey_question_add', ['id' => $survey->getId()]);
            }

            return $this->redirectToRoute('survey_index');
        }

        return $this->render('survey/add_question.html.twig', [
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
     * @Route("/author/results/{id}", name="survey_results", methods={"GET"})
     */
    public function surveyResults(Survey $survey, AttendSurveyRepository $attendSurveyRepository): Response
    {
        $questions_answers = array();

        $attended_surveys = $attendSurveyRepository->findBy(['survey' => $survey]);
        $all_questions = $survey->getQuestions();

        foreach ($all_questions as $question){
            foreach ($question->getAnswerOptions() as $answerOption){
                $index = $answerOption->getId();
                $questions_answers[$index] = 0;
            }
        }

        foreach ($attended_surveys as $item){
            foreach ($item->getAnswers() as $answer){
                $questions_answers[$answer->getId()]++;
            }
        }

        return $this->render('survey/results.html.twig', [
            'all_questions' => $all_questions,
            'all_answers' => $questions_answers,
        ]);
    }

    /**
     * @Route("/author/results/{id}", name="survey_erase_results", methods={"DELETE"})
     */
    public function surveyEraseResults(Survey $survey, AttendSurveyRepository $attendSurveyRepository): Response
    {
        $attended_surveys = $attendSurveyRepository->findBy(['survey' => $survey]);

        foreach ($attended_surveys as $item){
            $this->deleteSurveyResults($item);
        }

        return $this->redirectToRoute('survey_index');

    }

    private function deleteSurveyResults(AttendSurvey $attendSurvey){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($attendSurvey);
        $entityManager->flush();
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
