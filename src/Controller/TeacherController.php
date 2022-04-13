<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{
    #[Route('/enseignant/{id}', name: 'teacher',requirements:["id"=>"\d+"])]
    public function show($id,TeacherRepository $teacherRepository,): Response
    {

        $teacher = $teacherRepository->find($id);
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            'teacher'=> $teacher
        ]);
    }


    #[Route('/enseignant/{id}/cours', name: '.courses',requirements:["id"=>"\d+"])]
    public function courses($id,TeacherRepository $teacherRepository,): Response
    {

        $teacher = $teacherRepository->find($id);
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            'teacher'=> $teacher
        ]);
    }
}
