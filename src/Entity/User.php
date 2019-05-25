<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
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
     * @ORM\OneToMany(targetEntity="App\Entity\Receita", mappedBy="user", orphanRemoval=true)
     */
    private $Recipes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="isFollowedBy")
     */
    private $follow;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="follow")
     */
    private $isFollowedBy;

    public function __construct()
    {
        $this->Recipes = new ArrayCollection();
        $this->follow = new ArrayCollection();
        $this->isFollowedBy = new ArrayCollection();
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
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    /**
     * @return Collection|Receita[]
     */
    public function getRecipes(): Collection
    {
        return $this->Recipes;
    }

    public function addRecipe(Receita $recipe): self
    {
        if (!$this->Recipes->contains($recipe)) {
            $this->Recipes[] = $recipe;
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Receita $recipe): self
    {
        if ($this->Recipes->contains($recipe)) {
            $this->Recipes->removeElement($recipe);
            // set the owning side to null (unless already changed)
            if ($recipe->getUser() === $this) {
                $recipe->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollow(): Collection
    {
        return $this->follow;
    }

    public function addFollow(self $follow): self
    {
        if (!$this->follow->contains($follow)) {
            $this->follow[] = $follow;
        }

        return $this;
    }

    public function removeFollow(self $follow): self
    {
        if ($this->follow->contains($follow)) {
            $this->follow->removeElement($follow);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getIsFollowedBy(): Collection
    {
        return $this->isFollowedBy;
    }

    public function addIsFollowedBy(self $isFollowedBy): self
    {
        if (!$this->isFollowedBy->contains($isFollowedBy)) {
            $this->isFollowedBy[] = $isFollowedBy;
            $isFollowedBy->addFollow($this);
        }

        return $this;
    }

    public function removeIsFollowedBy(self $isFollowedBy): self
    {
        if ($this->isFollowedBy->contains($isFollowedBy)) {
            $this->isFollowedBy->removeElement($isFollowedBy);
            $isFollowedBy->removeFollow($this);
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'Id' => $this->getId(),
            'Email' => $this->getEmail()
        ];
    }
}
