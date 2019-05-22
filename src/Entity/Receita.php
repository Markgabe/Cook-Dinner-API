<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceitaRepository")
 */
class Receita implements \JsonSerializable
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
    private $Nome;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Descricao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avaliacao", mappedBy="Receita", orphanRemoval=true)
     */
    private $Avaliacao;

    public function __construct()
    {
        $this->Avaliacao = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): self
    {
        $this->Nome = $Nome;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->Descricao;
    }

    public function setDescricao(?string $Descricao): self
    {
        $this->Descricao = $Descricao;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'Nome' => $this->getNome(),
            'Descrição' => $this->getDescricao()
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
}
