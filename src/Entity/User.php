<?php

namespace App\Entity;

use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, JsonSerializable
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profile_picture;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="is_followed_by")
     */
    private $follows;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="follows")
     */
    private $is_followed_by;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->is_followed_by = new ArrayCollection();
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

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

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
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollows(self $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows[] = $follow;
        }

        return $this;
    }

    public function removeFollows(self $follow): self
    {
        if ($this->follows->contains($follow)) {
            $this->follows->removeElement($follow);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getIsFollowedBy(): Collection
    {
        return $this->is_followed_by;
    }

    public function addIsFollowedBy(self $isFollowedBy): self
    {
        if (!$this->is_followed_by->contains($isFollowedBy)) {
            $this->is_followed_by[] = $isFollowedBy;
            $isFollowedBy->addFollows($this);
        }

        return $this;
    }

    public function removeIsFollowedBy(self $isFollowedBy): self
    {
        if ($this->is_followed_by->contains($isFollowedBy)) {
            $this->is_followed_by->removeElement($isFollowedBy);
            $isFollowedBy->removeFollows($this);
        }

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

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'gender' => $this->getGender(),
            'birthday' => $this->getBirthday()->format('d-m-Y'),
            'profile_picture' => '/get_pic/'.$this->getId(),
            'created_at' => $this->getCreatedAt()->setTimezone(new DateTimeZone('America/Sao_Paulo'))->format("d-m-Y H:i:s"),
            'recipes' => $this->listSerialize($this->getRecipes()),
            'follows' => $this->listSerialize($this->getFollows()),
            'is_followed_by' => $this->listSerialize($this->getIsFollowedBy())
        ];
    }

    public function listSerialize($list)
    {
        $newArray = array();
        foreach ( $list as $item ) {
                array_push($newArray, $item);
        }
        return $newArray;
    }
}
