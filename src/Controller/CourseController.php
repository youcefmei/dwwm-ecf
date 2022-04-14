<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\CourseStudent;
use App\Repository\CourseRepository;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class CourseController extends AbstractController
{
    /** Show all published courses
     * 
     */
    #[Route('/formation', name: 'course')]
    public function index(Request $request,ManagerRegistry $doctrine): Response
    {
        $courseRepository = $doctrine->getRepository(Course::class);
        //Pagination
        $courses_all_size =  sizeof($courseRepository->findBy(["is_published"=>true],$orderBy=['published_at'=>'desc']));
        $pagination = $this->getPagination($request,$courses_all_size);
        extract($pagination);
        // courses
        
        $searching = $request->query->get("s");
        if (is_null($searching)){
            $courses = $courseRepository->findBy(["is_published"=>true],$orderBy=['published_at'=>'desc'],$limit=$limit,$offset = $offset); 
            return $this->render('course/index.html.twig', [
                'mainTitle'=> "NOTRE CATALOGUE",
                'courses' => $courses,
                'offset'=>$offset,
                'limit'=>$limit,
                'pages'=> $pages,
            ]);
        }
        else{
            $courses = $courseRepository->findByTitleLike($searching,$offset,$limit, true); 
            return $this->render('course/_courses.html.twig', [
                'mainTitle'=> "NOTRE CATALOGUE",
                'courses' => $courses,
                'offset'=>$offset,
                'limit'=>$limit,
                'pages'=> $pages,
            ]);
        }
    }

    /** A student enroll a course
     * 
     */
    #[Route('/formation/{slug}/inscription', name: 'course.enroll')]
    public function enroll(Course $course,ManagerRegistry $doctrine)
    {
        if ( in_array( 'ROLE_STUDENT' ,$this->getUser()->getRoles()) && $course->getIsPublished() ){
            
            $student = $doctrine->getRepository(Student::class)->findOneBy(["user"=>$this->getUser()]);
            if($student->getUser() == $this->getUser())
            {
                $entityManager = $doctrine->getManager();
                $courseStudent = new CourseStudent();
                $courseStudent->setCourse($course);
                $courseStudent->setStudent($student);
                $entityManager->persist($courseStudent);
                $student->addCourseStudent($courseStudent);
                $course->addCourseStudent($courseStudent);
                $entityManager->persist($student);
                $entityManager->persist($course);
                $entityManager->flush();
    
                $section = $course->getSections()->first();
                return $this->redirectToRoute("lesson",[
                    "slugCourse"=>$course->getSlug(),
                    "slugSection"=>$section->getSlug(),
                    "slugLesson"=>$section->getLessons()->first()->getSlug(),
                ]);
            }
        }
        
    }
    /** Show main page of a course
     * 
     */
    #[Route('/formation/{slug}', name: 'course.show')]
    public function show(Course $course,StudentRepository $studentRepository): Response
    {
        
        $sections = $course->getSections();
        $student = $studentRepository->findOneBy(["user"=>$this->getUser()]);
        if (!is_null($student) ) {
            $coursesOwnIds = [];
            $coursesStudents = $student->getCourseStudents();
            foreach ($coursesStudents as $courseStudent) {
                $coursesOwnIds[] = $courseStudent->getCourse()->getId();
            }
            if (in_array($course->getId(),$coursesOwnIds)){
                $section = $course->getSections()->first();
                return $this->redirectToRoute("lesson",[
                    "slugCourse"=>$course->getSlug(),
                    "slugSection"=>$section->getSlug(),
                    "slugLesson"=>$section->getLessons()->first()->getSlug(),
                ]);
            }
        }
        else{
            $coursesOwnIds = null; 
        }
        
        return $this->render('course/show.html.twig', [
            'course' => $course,
             'sections'=>$sections,
             "student" => $student,
             "coursesOwnIds" => $coursesOwnIds
        ]);
    }

    /** Shows courses created by a teacher
     * 
     */
    #[Route('/formations/formateur/{id<\d+>}', name: 'courses.teacher')]
    public function teacherCourses(Teacher $teacher,Request $request,CourseRepository $courseRepository): Response
    {
        if ($teacher->getUser() == $this->getUser()){            
            //Pagination
            $courses_all_size =  sizeof($teacher->getCourses());
            $pagination = $this->getPagination($request,$courses_all_size);
            extract($pagination);
            //
                    
            $searching = $request->query->get("s");
            if (is_null($searching)){
                $courses = $courseRepository->findBy(["teacher"=>$teacher],$orderBy=['created_at'=>'desc'],$limit=$limit,$offset = $offset); 
                return $this->render('course/index.html.twig', [
                    'mainTitle'=> "Mes Formations",
                    'courses' => $courses,
                    'offset'=>$offset,
                    'limit'=>$limit,
                    'pages'=> $pages,
                    'allow'=>true,
                    'personId'=>$teacher->getId()
                ]);
            }
            else{
                $courses = $courseRepository->findByTitleLike($searching,$offset,$limit,null); 
                return $this->render('course/_courses.html.twig', [
                    'mainTitle'=> "Mes Formations",
                    'courses' => $courses,
                    'offset'=>$offset,
                    'limit'=>$limit,
                    'pages'=> $pages,
                    'allow'=>true,
                    'personId'=>$teacher->getId()
                ]);
            }
        }
    }

    /**  show a student's courses
     * 
     */
    #[Route('/formations/etudiant/{id<\d+>}', name: 'courses.student')]
    public function studentCourses(Student $student,Request $request,ManagerRegistry $doctrine): Response
    {
        if ($student->getUser()==$this->getUser()){
            //Pagination
            $courseStudents = $student->getCourseStudents();
            $courses_all_size =  sizeof($courseStudents);
            $pagination = $this->getPagination($request,$courses_all_size);
            extract($pagination);
            //
            
            $searching = $request->query->get("s");
            $courses =[];
            if (is_null($searching)){
                
                foreach ($courseStudents as $courseStudent) {
                    $courses[] = $courseStudent->getCourse();
                }
                return $this->render('course/index.html.twig', [
                    'mainTitle'=> "Mes Formations",
                    'courses' => $courses,
                    'offset'=>$offset,
                    'limit'=>$limit,
                    'pages'=> $pages,
                    'personId'=>$student->getId()
                ]);
            }
            else{
                foreach ($courseStudents as $courseStudent) {
                    if (str_contains($courseStudent->getCourse() , $searching) ){
                        $courses[] = $courseStudent->getCourse();
                    }
                }
                return $this->render('course/_courses.html.twig', [
                    'mainTitle'=> "Mes Formations",
                    'courses' => $courses,
                    'offset'=>$offset,
                    'limit'=>$limit,
                    'pages'=> $pages,
                    'personId'=>$student->getId()
                ]);
            }
        }
    }
    
    /**
     * Gets the pagination
     *
     */
    private function getPagination(Request $request,int $nbElmts) : array
    {   
        $pagination = [];
        $offset = $request->query->get("offset");
        if ( is_null($offset) ){
            $offset=0;
        }
        $limit = $request->query->get("limit");
        if ( (is_null($limit)) || ($limit>50)|| ($limit<1) ){
            $limit=12;
        }
        $pages = intdiv($nbElmts ,$limit) ; 
        if ( ($limit * $pages) == $nbElmts ){
            $pages--;
        }
        $pagination['offset'] = $offset;
        $pagination['limit'] = $limit;
        $pagination['pages'] = $pages;
        return $pagination;
    }


}
