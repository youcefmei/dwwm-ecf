<?php

namespace App\Entity;

use App\Repository\LessonStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonStudentRepository::class)]
class LessonStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'lessonStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $lesson;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'lessonStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
