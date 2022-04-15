<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\CourseStudent;
use App\Entity\LessonStudent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LessonController extends AbstractController
{
    /**
     * Show a lesson 
     */
    #[Route('/formation/{slugCourse}/{slugSection}/{slugLesson}', name: 'lesson')]
    public function lesson(string $slugCourse, string $slugSection, string $slugLesson, ManagerRegistry $doctrine, Request $request): Response
    {



        if (is_null($this->getUser())) {
            return $this->redirectToRoute("login");
        }
        $roles = $this->getUser()->getRoles();
        $course = $doctrine->getRepository(Course::class)->findOneBy(["slug" => $slugCourse]);
        $section = $doctrine->getRepository(Section::class)->findOneBy(["slug" => $slugSection, "course" => $course,'teacher'=>$course->getTeacher()]);
        $teacherOwner = $course->getTeacher();
        // dd($teacherOwner);
        $lesson = $doctrine->getRepository(Lesson::class)->findOneBy(["slug" => $slugLesson, "section" => $section,'teacher'=>$course->getTeacher() ]);
        $isFinnished = null;
        $allow = false;
        if (in_array("ROLE_ADMIN", $roles)) {
            $allow = true;
        } else if (in_array("ROLE_TEACHER", $roles)) {
            $teacher = $doctrine->getRepository(Teacher::class)->findOneBy(["user" => $this->getUser()]);
            if ($teacher == $course->getTeacher()) {
                $allow = true;
            }
        } else if (in_array("ROLE_STUDENT", $roles)) {

            $student = $doctrine->getRepository(Student::class)->findOneBy(["user" => $this->getUser()]);
            $entityManager = $doctrine->getManager();
            foreach ($student->getCourseStudents() as $courseStudent) {
                if ($course == $courseStudent->getCourse()) {
                    $allow = true;
                    $isFinnished = false;
                    $lessonStudent = $doctrine->getRepository(LessonStudent::class)->findOneBy(["student" => $student, "lesson" => $lesson]);

                    if (!is_null($lessonStudent)) {
                        $isFinnished = true;
                    } else if ($request->query->get('lesson-complete') == true) {
                        $isFinnished = true;
                        $lessonStudent = new LessonStudent();
                        $lessonStudent->setStudent($student);
                        $lessonStudent->setLesson($lesson);
                        $entityManager->persist($lessonStudent);
                        $entityManager->flush();
                    }
                    break;
                }
            }
            if (!$allow  && $course->getIsPublished()) {
                // enroll a course
                $allow = true;
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
                return $this->redirectToRoute("lesson", [
                    "slugCourse" => $course->getSlug(),
                    "slugSection" => $section->getSlug(),
                    "slugLesson" => $section->getLessons()->first()->getSlug(),
                ]);
            }
        }
        if ($allow) {
            return $this->render('lesson/index.html.twig', [
                'course' => $course,
                'section' => $section,
                'lesson' => $lesson,
                'allow' => $allow,
                'isFinnished' => $isFinnished,
            ]);
        }
    }
}
