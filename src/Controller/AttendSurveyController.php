<?php

namespace App\Controller;

use App\Entity\AttendSurvey;
use App\Entity\Survey;
use App\Form\AttendSurveyType;
use App\Repository\AttendSurveyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/attend/survey")
 */
class AttendSurveyController extends AbstractController
{
    /**
     * @Route("/", name="attend_survey_index", methods={"GET"})
     */
    public function index(AttendSurveyRepository $attendSurveyRepository): Response
    {
        return $this->render('attend_survey/index.html.twig', [
            'attend_surveys' => $attendSurveyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/attend_survey/{id}", name="attend_survey_new", methods={"GET","POST"})
     */
    public function new(Request $request, Survey $survey): Response
    {
        $attendSurvey = new AttendSurvey();
        $attendSurvey->setSurvey($survey);
        $attendSurvey->setUser($this->getUser());
        $form = $this->createForm(AttendSurveyType::class, $attendSurvey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attendSurvey);
            $entityManager->flush();

            return $this->redirectToRoute('attend_survey_index');
        }

        return $this->render('attend_survey/new.html.twig', [
            'attend_survey' => $attendSurvey,
            'form' => $form->createView(),
            'survey' => $survey,
        ]);
    }

    /**
     * @Route("/{id}", name="attend_survey_show", methods={"GET"})
     */
    public function show(AttendSurvey $attendSurvey): Response
    {
        return $this->render('attend_survey/show.html.twig', [
            'attend_survey' => $attendSurvey,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attend_survey_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AttendSurvey $attendSurvey): Response
    {
        $form = $this->createForm(AttendSurveyType::class, $attendSurvey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attend_survey_index');
        }

        return $this->render('attend_survey/edit.html.twig', [
            'attend_survey' => $attendSurvey,
            'form' => $form->createView(),
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

        return $this->redirectToRoute('attend_survey_index');
    }
}
