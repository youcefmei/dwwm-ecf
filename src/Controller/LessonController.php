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
        $teacherOwner = $course->getTeacher();
        $section = $doctrine->getRepository(Section::class)->findOneBy(["slug" => $slugSection, "course" => $course, 'teacher' => $teacherOwner]);

        $lesson = $doctrine->getRepository(Lesson::class)->findOneBy(["slug" => $slugLesson, "section" => $section, 'teacher' => $teacherOwner]);
        $isFinnished = null;
        $allow = false;
        if (in_array("ROLE_ADMIN", $roles)) {
            $allow = true;
        } else if (in_array("ROLE_TEACHER", $roles)) {
            $teacher = $doctrine->getRepository(Teacher::class)->findOneBy(["user" => $this->getUser()]);
            if ($teacher == $teacherOwner)
            {
                $allow = true;
            }
        } else if (in_array("ROLE_STUDENT", $roles) && $course->getIsPublished() && $section->getIsPublished() && $lesson->getIsPublished()) {
            
            $student = $doctrine->getRepository(Student::class)->findOneBy(["user" => $this->getUser()]);
            $courseStudent = $doctrine->getRepository(CourseStudent::class)->findOneBy(["student" => $student, 'course' => $course]);
            $entityManager = $doctrine->getManager();

            // $lessonStudentFinnished = $doctrine->getRepository(LessonStudent::class)->findBy(["student" => $student]);
            $lessonStudentFinnished = $doctrine->getRepository(LessonStudent::class)->findLessonsByStudent($student);
            $lessonsCompleted = [];

            $courseProgress = 0;
            $totalLessons = 0;
            foreach ($course->getSections() as $keySection => $sectionTemp) {
                if ($sectionTemp->getIsPublished()) {
                    foreach ($sectionTemp->getLessons() as $keyLesson => $lessonTemp) {
                        if ($lessonTemp->getIsPublished()) {
                            $totalLessons += 1;
                            if (in_array($lessonTemp->getId(), $lessonStudentFinnished)) {
                                $lessonsCompleted[] = $lessonTemp;
                            }
                        }
                    }
            
                }
            }
            if (sizeof($lessonsCompleted) and $totalLessons) {
                $courseProgress = (sizeof($lessonsCompleted) / $totalLessons) * 100;
            }
            // dd($courseProgress);
            if ($courseStudent) {
                $allow = true;
                $isFinnished = false;
                $lessonStudent = $doctrine->getRepository(LessonStudent::class)->findOneBy(["student" => $student, "lesson" => $lesson]);
                if ($lessonStudent) {
                    
                    if ($request->query->get('lesson-complete') === 'false') {
                        if (($key = array_search($lesson, $lessonsCompleted)) !== false) {
                            unset($lessonsCompleted[$key]);
                        }
                        $courseProgress = ((sizeof($lessonsCompleted)) / $totalLessons) * 100;
                        $courseStudent = $doctrine->getRepository(CourseStudent::class)->findOneBy(["student" => $student, "course" => $course]);
                        $courseStudent->setProgress($courseProgress);
                        $doctrine->getRepository(LessonStudent::class)->remove($lessonStudent);
                        $entityManager->persist($courseStudent);
                        $entityManager->persist($student);
                        $entityManager->flush();
                    } 
                    
                    else {
                        $isFinnished = true;
                    }
                } 
                else if ($request->query->get('lesson-complete') === 'true') {

                    // dd($request->query);
                    $isFinnished = true;
                    $lessonsCompleted[] = $lesson;
                    $courseProgress = ((sizeof($lessonsCompleted) ) / $totalLessons) * 100;
                    $lessonStudent = new LessonStudent();
                    $courseStudent = $doctrine->getRepository(CourseStudent::class)->findOneBy(["student" => $student, "course" => $course]);
                    $courseStudent->setProgress($courseProgress);
                    $lessonStudent->setStudent($student);
                    $lessonStudent->setLesson($lesson);
                    $entityManager->persist($lessonStudent);
                    $entityManager->persist($courseStudent);
                    $entityManager->flush();
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

        $courseProgress = isset($courseProgress) ?  floor($courseProgress) : null;
        $lessonsCompleted = isset($lessonsCompleted) ? $lessonsCompleted : null;
        if ($allow) {
            return $this->render('lesson/index.html.twig', [
                'course' => $course,
                'section' => $section,
                'lesson' => $lesson,
                'allow' => $allow,
                'isFinnished' => $isFinnished,
                'courseProgress' => $courseProgress,
                'lessonsCompleted' => $lessonsCompleted
            ]);
        }
    }
}
