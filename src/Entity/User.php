<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $lastName;

    /**
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Survey", mappedBy="creator")
     */
    private $surveys;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttendSurvey", mappedBy="user")
     */
    private $attendSurveys;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
        $this->attendSurveys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): self
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys[] = $survey;
            $survey->setCreator($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): self
    {
        if ($this->surveys->contains($survey)) {
            $this->surveys->removeElement($survey);
            // set the owning side to null (unless already changed)
            if ($survey->getCreator() === $this) {
                $survey->setCreator(null);
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
            $attendSurvey->setUser($this);
        }

        return $this;
    }

    public function removeAttendSurvey(AttendSurvey $attendSurvey): self
    {
        if ($this->attendSurveys->contains($attendSurvey)) {
            $this->attendSurveys->removeElement($attendSurvey);
            // set the owning side to null (unless already changed)
            if ($attendSurvey->getUser() === $this) {
                $attendSurvey->setUser(null);
            }
        }

        return $this;
    }
}
