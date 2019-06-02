<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recipe", mappedBy="user", orphanRemoval=true)
     */
    private $recipes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="isFollowedBy")
     */
    private $follow;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="follow")
     */
    private $isFollowedBy;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthday(): ?DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        $this->created_at = new DateTime();

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->contains($recipe)) {
            $this->recipes->removeElement($recipe);
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
}
