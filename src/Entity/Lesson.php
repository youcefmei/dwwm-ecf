<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'datetime_immutable')]
    private $update_at;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private $is_published;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private $section;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $media;

    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: LessonStudent::class, orphanRemoval: true)]
    private $lessonStudents;



    public function __construct()
    {
        $this->update_at = new \DateTimeImmutable();
        $this->lessonStudents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        $this->update_at = new \DateTimeImmutable();
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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeImmutable $update_at): self
    {
        $this->update_at = $update_at;

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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
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

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(string $media): self
    {
        $this->media = $media;

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
            $lessonStudent->setLesson($this);
        }

        return $this;
    }

    public function removeLessonStudent(LessonStudent $lessonStudent): self
    {
        if ($this->lessonStudents->removeElement($lessonStudent)) {
            // set the owning side to null (unless already changed)
            if ($lessonStudent->getLesson() === $this) {
                $lessonStudent->setLesson(null);
            }
        }

        return $this;
    }




}
