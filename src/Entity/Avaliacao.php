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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Nota;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Favorito;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="Avaliacao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Receita;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNota(): ?int
    {
        return $this->Nota;
    }

    public function setNota(?int $Nota): self
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

    public function getReceita(): ?Recipe
    {
        return $this->Receita;
    }

    public function setReceita(?Recipe $Receita): self
    {
        $this->Receita = $Receita;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'Id' => $this->getId(),
            'Nota' => $this->getNota(),
            'Favorito' => $this->getFavorito(),
            'Id_Receita' => $this->getReceita()->getId()
        ];
    }
}
