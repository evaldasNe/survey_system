<?php

namespace App\Controller;

use App\Repository\AttendSurveyRepository;
use App\Repository\SurveyRepository;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(SurveyRepository $surveyRepository, AttendSurveyRepository $attendSurveyRepository)
    {
        $attended_surveys = array();
        if ($this->getUser() != null){
            $attended = $attendSurveyRepository->findBy(['user' => $this->getUser()]);
            foreach ($attended as $item){
                array_push($attended_surveys, $item->getSurvey());
            }
        }
        return $this->render('home_page/index.html.twig', [
            'surveys' => $surveyRepository->findAll(),
            'attended_surveys' => $attended_surveys,
        ]);
    }
}
