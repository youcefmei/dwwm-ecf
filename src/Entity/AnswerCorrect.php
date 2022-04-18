<?php

namespace App\Entity;

use App\Repository\AnswerCorrectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerCorrectRepository::class)]
class AnswerCorrect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\OneToOne(mappedBy: 'answercorrect', targetEntity: Question::class, cascade: ['persist', 'remove'])]
    private $question;


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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        // set the owning side of the relation if necessary
        if ($question->getAnswercorrect() !== $this) {
            $question->setAnswercorrect($this);
        }

        $this->question = $question;

        return $this;
    }

}
