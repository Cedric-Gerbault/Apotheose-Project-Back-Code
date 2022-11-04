<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Game;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @Groups("user")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * 
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * 
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups("user")
     * @ORM\Column(type="date")
     */
    private $Birthdate;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $platform;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, * inversedBy="userChecklist")
     * 
     * @ORM\JoinTable(
     *      name="checklist",
     *      joinColumns={
     *          @ORM\JoinColumn(
     *              name="checklist",
     *              referencedColumnName="id"
     *          )
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(
     *              name="userChecklist",
     *              referencedColumnName="id"
     *          )
     *      }
     *  )
     */
    private $checklist;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, * inversedBy="userWishlist")
     * 
     * @ORM\JoinTable(
     *      name="wishlist",
     *      joinColumns={
     *          @ORM\JoinColumn(
     *              name="wishlist",
     *              referencedColumnName="id"
     *          )
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(
     *              name="userWishlist",
     *              referencedColumnName="id"
     *          )
     *      }
     *  )
     */
    private $wishlist;

    public function __construct()
    {
        $this->checklist = new ArrayCollection();
        $this->wishlist = new ArrayCollection();
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->Birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $Birthdate): self
    {
        $this->Birthdate = $Birthdate;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(?string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getChecklist(): Collection
    {
        return $this->checklist;
    }

    public function addChecklist(Game $checklist): self
    {
        if (!$this->checklist->contains($checklist)) {
            $this->checklist[] = $checklist;
        }

        return $this;
    }

    public function removeChecklist(Game $checklist): self
    {
        $this->checklist->removeElement($checklist);

        return $this;
    }

/**
     * @return Collection<int, User>
     */
    public function getwishlist(): Collection
    {
        return $this->wishlist;
    }

    public function addWishlist(Game $wishlist): self
    {
        if (!$this->wishlist->contains($wishlist)) {
            $this->wishlist[] = $wishlist;
        }

        return $this;
    }

    public function removeWishlist(Game $wishlist): self
    {
        $this->wishlist->removeElement($wishlist);


        return $this;
    }
}
