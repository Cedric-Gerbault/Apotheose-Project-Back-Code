<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recommended;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="checklist")
     */
    private $userChecklist;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="wishlist")
     */
    private $userWishlist;

    /**
     * @ORM\Column(type="integer")
     */
    private $apiId;

    public function __construct()
    {
        $this->userChecklist = new ArrayCollection();
        $this->userWishlist = new ArrayCollection();
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
        $this->name = $name;

        return $this;
    }

    public function isRecommended(): ?bool
    {
        return $this->recommended;
    }

    public function setRecommended(bool $recommended): self
    {
        $this->recommended = $recommended;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserChecklist(): Collection
    {
        return $this->userChecklist;
    }

    public function addUserChecklist(User $userChecklist): self
    {
        if (!$this->userChecklist->contains($userChecklist)) {
            $this->usersChecklist[] = $userChecklist;
            $userChecklist->addChecklist($this);
        }

        return $this;
    }

    public function removeUserChecklist(User $userChecklist): self
    {
        if ($this->userChecklist->removeElement($userChecklist)) {
            $userChecklist->removeChecklist($this);
        }

        return $this;
    }

        /**
     * @return Collection<int, Game>
     */
    public function getUserWishlist(): Collection
    {
        return $this->userWishlist;
    }

    public function addWishlist(Game $userWishlist): self
    {
        if (!$this->$userWishlist->contains($userWishlist)) {
            $this->userWishlist[] = $userWishlist;
        }

        return $this;
    }

    public function removeWishlist(Game $userWishlist): self
    {
        $this->userWishlist->removeElement($userWishlist);

        return $this;
    }

    public function getApiId(): ?int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): self
    {
        $this->apiId = $apiId;

        return $this;
    }
    
}
