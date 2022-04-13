<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{


    // #[Route('/login', name: 'login',methods:["GET"])]
    // public function loginGet(AuthenticationUtils $authenticationUtils,Request $request): Response
    // {
    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();
    //     // if ($form->isSubmitted() && $form->isValid()) {
    //     //     dd($request);
    //     // }

    //     // if ($this->isGranted("ROLE_TEACHER")){
    //     //     yield MenuItem::linkToCrud('Formateurs', 'fas fa-list', Teacher::class);
    //     // }

    //     return $this->render('login/index.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error'         => $error,
    //         'controller_name' => 'LoginController',
    //     ]);
    // }

    #[Route('/login', name: 'login', methods: ["GET", "POST"])]
    public function loginPost(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        // dd($this->getUser());
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute("admin");
        } else if ($this->isGranted("ROLE_TEACHER")) {

            return $this->redirectToRoute("admin");
            // dd($this->getUser());
        } else if ($this->isGranted("ROLE_STUDENT")) {
            return $this->redirectToRoute("home");
        } 
        // else if ($this->isGranted("ROLE_TEACHER_PENDING")) {
            // $this->addFlash("warning", 'Votre demande a bien été prise en compte.Nous étudions votre dossier, nous vous recontracterons prochainement.');
            // dd($this);
            // return $this->redirectToRoute("home");
        // }


        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'controller_name' => 'LoginController',
            "is_teacher_pending"=>$this->isGranted("ROLE_TEACHER_PENDING"),
        ]);


        // do anything else you need here, like send an email

    }



    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
    }
}
