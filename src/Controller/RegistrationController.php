<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\AdminRepository;
use App\Form\RegistrationAdminFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationStudentFormType;
use App\Form\RegistrationTeacherFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * Student register route
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/etudiant/enregistrement', name: 'student.register')]
    public function studentRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(RegistrationStudentFormType::class, $student);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $student->getUser()->setPassword($userPasswordHasher->hashPassword($student->getUser(), $form->get('user')->get('plainPassword')->getData()));
            $student->getUser()->setRoles(["ROLE_STUDENT"]);
            $entityManager->persist($student);
            $entityManager->flush();
        }
        return $this->render('registration/register_student.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * Teacher register route
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/formateur/enregistrement', name: 'teacher.register')]
    public function teacherRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(RegistrationTeacherFormType::class, $teacher);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $teacher->getUser()->setPassword(
                $userPasswordHasher->hashPassword(
                    $teacher->getUser(),
                    $form->get('user')->get('plainPassword')->getData()
                )
            );
            $teacher->getUser()->setRoles(["ROLE_TEACHER_PENDING"]);
            $entityManager->persist($teacher);
            $entityManager->flush();
            $this->addFlash("success", 'Votre demande a bien été prise en compte .Nous vous remercions de l\'intérêt que vous portez à notre société', 'Nous allons étudier votre dossier puis nous vous recontacterons par mail.');
            // do anything else you need here, like send an email
            return $this->redirectToRoute('admin');
        }
        // dd($form->createView());
        return $this->render('registration/register_teacher.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Admin register route
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param AdminRepository $adminRepo
     * @return Response
     */
    #[Route('/admin/enregistrement', name: 'admin.register')]
    public function adminRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, AdminRepository $adminRepo): Response
    {
        $admin = new Admin();
        $form = $this->createForm(RegistrationAdminFormType::class, $admin);
        $form->handleRequest($request);

        // Only one admin is allow to register 
        if (!empty($adminRepo->findAll())) {
            return $this->redirectToRoute("home");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->getUser()->setEmail($form->get('user')->get('email')->getData());
            $admin->getUser()->setPassword($userPasswordHasher->hashPassword($admin->getUser(), $form->get('user')->get('plainPassword')->getData()));
            $admin->getUser()->setRoles(["ROLE_ADMIN"]);
            $entityManager->persist($admin);
            $entityManager->flush();
            return $this->redirectToRoute("admin");
        }

        return $this->render('registration/register_admin.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

}
