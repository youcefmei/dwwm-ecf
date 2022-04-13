<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
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

    // #[ORM\Column(type: 'datetime_immutable',options: [ "default" => new \DateTimeImmutable()])]
    // private $created_at;

    // #[ORM\Column(type: 'boolean')]
    // private $is_published;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: true)]
    private $course;

    // #[ORM\OneToMany(mappedBy: 'section', targetEntity: Lesson::class, orphanRemoval: true)]
    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Lesson::class)]
    private $lessons;

    #[ORM\OneToOne(mappedBy: 'section', targetEntity: Quiz::class, cascade: ['persist', 'remove'])]
    private $quiz;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $added_at;

    // #[ORM\Column(type: 'integer')]
    // private $num_order;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->added_at = new \DateTimeImmutable();
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

    // public function getCreatedAt(): ?\DateTimeImmutable
    // {
    //     return $this->created_at;
    // }

    // public function setCreatedAt(\DateTimeImmutable $created_at): self
    // {
    //     $this->created_at = $created_at;

    //     return $this;
    // }

    // public function getIsPublished(): ?bool
    // {
    //     return $this->is_published;
    // }

    // public function setIsPublished(bool $is_published): self
    // {
    //     $this->is_published = $is_published;

    //     return $this;
    // }

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

    // public function getNumOrder(): ?int
    // {
    //     return $this->num_order;
    // }

    // public function setNumOrder(int $num_order): self
    // {
    //     $this->num_order = $num_order;

    //     return $this;
    // }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->added_at;
    }

    public function setAddedAt(?\DateTimeImmutable $added_at): self
    {
        $this->added_at = $added_at;

        return $this;
    }
}
