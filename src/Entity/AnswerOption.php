<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerOptionRepository")
 */
class AnswerOption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answerOptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\AttendSurvey", mappedBy="answers")
     */
    private $attendSurveys;

    public function __construct()
    {
        $this->attendSurveys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection|AttendSurvey[]
     */
    public function getAttendSurveys(): Collection
    {
        return $this->attendSurveys;
    }

    public function addAttendSurvey(AttendSurvey $attendSurvey): self
    {
        if (!$this->attendSurveys->contains($attendSurvey)) {
            $this->attendSurveys[] = $attendSurvey;
            $attendSurvey->addAnswer($this);
        }

        return $this;
    }

    public function removeAttendSurvey(AttendSurvey $attendSurvey): self
    {
        if ($this->attendSurveys->contains($attendSurvey)) {
            $this->attendSurveys->removeElement($attendSurvey);
            $attendSurvey->removeAnswer($this);
        }

        return $this;
    }
}
