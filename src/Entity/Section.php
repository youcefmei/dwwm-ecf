<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SectionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ORM\UniqueConstraint(
  name: 'course_title_idx',
  columns: ['title', 'course_id']
)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;


    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'sections',cascade:['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $course;

    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Lesson::class, orphanRemoval: true,cascade:['remove'])]
    #[Assert\Unique]
    #[Assert\Count(
        min: 1,
        minMessage: 'Il faut au moins une leçon',
    )]
    private $lessons;

    #[ORM\OneToOne(mappedBy: 'section', targetEntity: Quiz::class)]
    private $quiz;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $added_at;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private $teacher;

    #[ORM\Column(type: 'boolean')]
    private $is_published;


    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->added_at = new \DateTimeImmutable();
        $this->is_published = true;
    }

    
    public function __toString()
    {
        return $this->title;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        if(!$this->slug){
            $this->slug= (new AsciiSlugger())->slug($title);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setSection($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getSection() === $this) {
                $lesson->setSection(null);
            }
        }

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): self
    {
        // set the owning side of the relation if necessary
        if ($quiz->getSection() !== $this) {
            $quiz->setSection($this);
        }

        $this->quiz = $quiz;

        return $this;
    }


    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->added_at;
    }

    public function setAddedAt(?\DateTimeImmutable $added_at): self
    {
        $this->added_at = $added_at;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;

        return $this;
    }
}
