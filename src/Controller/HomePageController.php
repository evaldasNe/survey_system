<?php

namespace App\Controller;

use App\Repository\SurveyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(SurveyRepository $surveyRepository)
    {
        return $this->render('home_page/index.html.twig', [
            'surveys' => $surveyRepository->findAll(),
        ]);
    }
}
