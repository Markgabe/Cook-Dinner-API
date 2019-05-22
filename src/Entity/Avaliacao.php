<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvaliacaoRepository")
 */
class Avaliacao implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Nota;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Favorito;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Receita", inversedBy="Avaliacao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Receita;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNota(): ?float
    {
        return $this->Nota;
    }

    public function setNota(?float $Nota): self
    {
        $this->Nota = $Nota;

        return $this;
    }

    public function getFavorito(): ?bool
    {
        return $this->Favorito;
    }

    public function setFavorito(bool $Favorito): self
    {
        $this->Favorito = $Favorito;

        return $this;
    }

    public function getReceita(): ?Receita
    {
        return $this->Receita;
    }

    public function setReceita(?Receita $Receita): self
    {
        $this->Receita = $Receita;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'Nota' => $this->getNota(),
            'Favorito' => $this->getFavorito()
        ];
    }
}
