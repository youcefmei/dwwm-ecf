<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findBy(["is_published"=>true],$orderBy=['published_at'=>'desc'],limit:3);
                             
        return $this->render('home/index.html.twig', [
            'courses' => $courses,
        ]);
    }


    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {

        return $this->render('about/index.html.twig');
    }
}
