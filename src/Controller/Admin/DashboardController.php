<?php

namespace App\Controller\Admin;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\Question;
use Doctrine\ORM\EntityManager;
use App\Repository\AdminRepository;
use App\Repository\TeacherRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private TeacherRepository $teacherRepository)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_TEACHER'))){
            return $this->redirectToRoute('home');
        }
        $teacher = $this->teacherRepository->findOneBy(['user'=>$this->getUser()]);

        return $this->render("admin/dashboard.html.twig",["teacher"=>$teacher]);


        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Eco IT')
            
            ;
    }

    public function configureMenuItems(): iterable
    {
        $teacher = $this->teacherRepository->findOneBy(['user'=>$this->getUser()]);
        if ($this->isGranted("ROLE_ADMIN")){
            yield MenuItem::linkToRoute('Accueil', 'fa  fa-external-link', 'home');
            yield MenuItem::section('');
            yield MenuItem::linkToCrud('Formateurs', 'fas fa-list', Teacher::class);
        }
        else if ($this->isGranted("ROLE_TEACHER")){            
            yield MenuItem::linkToRoute('Mes Formations', 'fa  fa-external-link', 'courses.teacher',['id'=>$teacher->getId()]);
            yield MenuItem::section('');
            yield MenuItem::subMenu('Formations', 'fas fa-school')->setSubItems([
                MenuItem::linkToCrud('Toutes mes formations', 'fas fa-list', Course::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Course::class)->setAction(Crud::PAGE_NEW)
            ]);

            yield MenuItem::subMenu('Sections', 'fas fa-heading')->setSubItems([
                MenuItem::linkToCrud('Toutes mes sections', 'fas fa-list', Section::class),
                // MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Section::class)->setAction(Crud::PAGE_NEW),
                // MenuItem::linkToCrud('Ajouter un Quiz', 'fas fa-plus', Quiz::class)->setAction(Crud::PAGE_NEW)
            ]);


            yield MenuItem::subMenu('Leçons', 'fas fa-graduation-cap')->setSubItems([
                MenuItem::linkToCrud('Toutes mes leçons', 'fas fa-list', Lesson::class),
                // MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Lesson::class)->setAction(Crud::PAGE_NEW)
            ]);
            yield MenuItem::section("");
            yield MenuItem::subMenu('Quiz', 'fas fa-chess')->setSubItems([
                MenuItem::linkToCrud('Tous mes quiz', 'fas fa-list', Quiz::class),
                // MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Quiz::class)->setAction(Crud::PAGE_NEW)
            ]);

            yield MenuItem::subMenu('Questions', 'fas fa-question')->setSubItems([
                MenuItem::linkToCrud('Toutes mes questions', 'fas fa-list', Question::class),
                // MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Question::class)->setAction(Crud::PAGE_NEW)
            ]);

            yield MenuItem::subMenu('Réponses', 'fas fa-sun')->setSubItems([
                MenuItem::linkToCrud('Toutes mes réponses', 'fas fa-list', Answer::class),
                // MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Answer::class)->setAction(Crud::PAGE_NEW)
            ]);

                    
            
        }
    }
}
