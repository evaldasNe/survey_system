<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="surveys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="survey", cascade={"persist"})
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttendSurvey", mappedBy="survey")
     */
    private $attendSurveys;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->attendSurveys = new ArrayCollection();
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

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setSurvey($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getSurvey() === $this) {
                $question->setSurvey(null);
            }
        }

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
            $attendSurvey->setSurvey($this);
        }

        return $this;
    }

    public function removeAttendSurvey(AttendSurvey $attendSurvey): self
    {
        if ($this->attendSurveys->contains($attendSurvey)) {
            $this->attendSurveys->removeElement($attendSurvey);
            // set the owning side to null (unless already changed)
            if ($attendSurvey->getSurvey() === $this) {
                $attendSurvey->setSurvey(null);
            }
        }

        return $this;
    }

}
