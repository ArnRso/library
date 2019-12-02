<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le nom doit contenir au minimum {{ limit }} caractères.",
     *      maxMessage = "Le nom doit contenir au maximum {{ limit }} caractères."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le prénom doit contenir au minimum {{ limit }} caractères.",
     *      maxMessage = "Le prénom doit contenir au maximum {{ limit }} caractères."
     * )
     * @Assert\Range(
     *     max="now",
     *     maxMessage="La date est dans le turfu."
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Expression(
     *     "value > this.getBirthDate()",
     *     message="La date de décès est antérieure à la date de naissance.",
     * )
     * @Assert\Expression(
     *     "value < this.getMaxDeathDate()",
     *     message="La date de décès est trop éloignée de la date de naissance."
     * )
     * @Assert\Range(
     *     max="now",
     *     maxMessage="La date est dans le turfu."
     * )
     */
    private $deathDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="author")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getBooks()
    {
        return $this->books;
    }

    public function getMaxDeathDate()
    {
        return $this->getBirthDate()->add(new \DateInterval('P100Y'));

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return ($this->getFirstName()) . " " . ($this->getName());
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
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


    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getDeathDate(): ?DateTimeInterface
    {
        return $this->deathDate;
    }

    public function setDeathDate(?DateTimeInterface $deathDate): self
    {
        $this->deathDate = $deathDate;
        return $this;
    }
}
