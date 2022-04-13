<?php

namespace App\Entity;

use App\Repository\CourseStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseStudentRepository::class)]
class CourseStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'courseStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $course;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'courseStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;

    #[ORM\Column(type: 'datetime_immutable')]
    private $joined_at;

    #[ORM\Column(type: 'smallint')]
    private $progress;

    public function __construct()
    {
        $this->joined_at = new \DateTimeImmutable();
        $this->progress = 0;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;
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

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joined_at;
    }

    public function setJoinedAt(\DateTimeImmutable $joined_at): self
    {
        $this->joined_at = $joined_at;

        return $this;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): self
    {
        $this->progress = $progress;

        return $this;
    }
}
