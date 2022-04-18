<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private $teacher;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: AnswerWrong::class, orphanRemoval: true)]
    private $answerswrongs;

 
    #[ORM\Column(type: 'string', length: 255)]
    private $correct_response;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    private $quiz;

    public function __construct()
    {
        $this->answerswrongs = new ArrayCollection();
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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, answerwrong>
     */
    public function getAnswerswrongs(): Collection
    {
        return $this->answerswrongs;
    }

    public function addAnswerswrong(answerwrong $answerswrong): self
    {
        if (!$this->answerswrongs->contains($answerswrong)) {
            $this->answerswrongs[] = $answerswrong;
            $answerswrong->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswerswrong(answerwrong $answerswrong): self
    {
        if ($this->answerswrongs->removeElement($answerswrong)) {
            // set the owning side to null (unless already changed)
            if ($answerswrong->getQuestion() === $this) {
                $answerswrong->setQuestion(null);
            }
        }

        return $this;
    }

    public function getCorrectResponse(): ?string
    {
        return $this->correct_response;
    }

    public function setCorrectResponse(string $correct_response): self
    {
        $this->correct_response = $correct_response;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }
}
