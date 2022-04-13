<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudentRepository;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[UniqueEntity(fields: 'username', message: 'Ce nom d\'utilisateur existe déjà!')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[ORM\OneToOne(inversedBy: 'student', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Valid]
    private $user;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: CourseStudent::class, orphanRemoval: true)]
    private $courseStudents;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: LessonStudent::class, orphanRemoval: true)]
    private $lessonStudents;


    public function __construct()
    {
        $this->user = new User();
        $this->courseStudents = new ArrayCollection();
        $this->lessonStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        
        $this->user = $user;
        return $this;
    }
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return Collection<int, CourseStudent>
     */
    public function getCourseStudents(): Collection
    {
        return $this->courseStudents;
    }

    public function addCourseStudent(CourseStudent $courseStudent): self
    {
        if (!$this->courseStudents->contains($courseStudent)) {
            $this->courseStudents[] = $courseStudent;
            $courseStudent->setStudent($this);
        }

        return $this;
    }

    public function removeCourseStudent(CourseStudent $courseStudent): self
    {
        if ($this->courseStudents->removeElement($courseStudent)) {
            // set the owning side to null (unless already changed)
            if ($courseStudent->getStudent() === $this) {
                $courseStudent->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LessonStudent>
     */
    public function getLessonStudents(): Collection
    {
        return $this->lessonStudents;
    }

    public function addLessonStudent(LessonStudent $lessonStudent): self
    {
        if (!$this->lessonStudents->contains($lessonStudent)) {
            $this->lessonStudents[] = $lessonStudent;
            $lessonStudent->setStudent($this);
        }

        return $this;
    }

    public function removeLessonStudent(LessonStudent $lessonStudent): self
    {
        if ($this->lessonStudents->removeElement($lessonStudent)) {
            // set the owning side to null (unless already changed)
            if ($lessonStudent->getStudent() === $this) {
                $lessonStudent->setStudent(null);
            }
        }

        return $this;
    }


}
