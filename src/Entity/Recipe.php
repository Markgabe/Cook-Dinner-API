<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 */
class Recipe implements \JsonSerializable
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avaliacao", mappedBy="Receita", orphanRemoval=true)
     */
    private $Avaliacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->Avaliacao = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->Nome = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $Description): self
    {
        $this->description = $Description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'Id' => $this->getId(),
            'Nome' => $this->getNome(),
            'Descrição' => $this->getDescricao(),
            'IdUser' => $this->getUser()->getId()
        ];
    }

    /**
     * @return Collection|Avaliacao[]
     */
    public function getAvaliacao(): Collection
    {
        return $this->Avaliacao;
    }

    public function addAvaliacao(Avaliacao $avaliacao): self
    {
        if (!$this->Avaliacao->contains($avaliacao)) {
            $this->Avaliacao[] = $avaliacao;
            $avaliacao->setReceita($this);
        }

        return $this;
    }

    public function removeAvaliacao(Avaliacao $avaliacao): self
    {
        if ($this->Avaliacao->contains($avaliacao)) {
            $this->Avaliacao->removeElement($avaliacao);
            // set the owning side to null (unless already changed)
            if ($avaliacao->getReceita() === $this) {
                $avaliacao->setReceita(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        $this->created_at = new DateTime();

        return $this;
    }
}
