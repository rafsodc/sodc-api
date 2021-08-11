<?php

namespace App\Entity;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Filters\UserFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "post"={
 *                  "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *                  "validation_groups"={"Default", "create_user"}
 *          },
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "patch"={"security"="is_granted('USER_EDIT', object)"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 * )
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ApiFilter(UserFilter::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:write", "user:read"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Groups("user:write")
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create_user"})
     */
    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:write", "user:read"})
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[a-zA-Z0-9_]+$/"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"user:write", "user:read"})
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="owner", orphanRemoval=true)
     */
    private $tickets;

    /**
     * Returns true if this is the currently authenticated user
     *
     * @Groups({"user:read"})
     */
    private $isMe = false;

    /**
     * @ORM\OneToMany(targetEntity=Basket::class, mappedBy="owner")
     */
    private $baskets;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postNominals;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $serviceNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $modnetEmail;

    /**
     * @ORM\ManyToOne(targetEntity=Rank::class, inversedBy="users")
     */
    private $rank;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $workDetails;

    /**
     * @Groups("user:write")
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(groups={"create_user"})
     */
    private $isShared;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldUid;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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
        return (string) $this->username;
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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return self
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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
        $this->plainPassword = null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setOwner($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getOwner() === $this) {
                $ticket->setOwner(null);
            }
        }

        return $this;
    }

    public function getIsMe(): bool
    {
        // if ($this->isMe === null) {
        //     throw new \LogicException('The isMe field has not been initialized');
        // }

        return $this->isMe;
    }
    public function setIsMe(bool $isMe)
    {
        $this->isMe = $isMe;
    }

    /**
     * @return Collection|Basket[]
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets[] = $basket;
            $basket->setOwner($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getOwner() === $this) {
                $basket->setOwner(null);
            }
        }

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPostNominals(): ?string
    {
        return $this->postNominals;
    }

    public function setPostNominals(?string $postNominals): self
    {
        $this->postNominals = $postNominals;

        return $this;
    }

    public function getServiceNumber(): ?string
    {
        return $this->serviceNumber;
    }

    public function setServiceNumber(?string $serviceNumber): self
    {
        $this->serviceNumber = $serviceNumber;

        return $this;
    }

    public function getModnetEmail(): ?string
    {
        return $this->modnetEmail;
    }

    public function setModnetEmail(?string $modnetEmail): self
    {
        $this->modnetEmail = $modnetEmail;

        return $this;
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getWorkDetails(): ?string
    {
        return $this->workDetails;
    }

    public function setWorkDetails(?string $workDetails): self
    {
        $this->workDetails = $workDetails;

        return $this;
    }

    public function getIsShared(): ?bool
    {
        return $this->isShared;
    }

    public function setIsShared(bool $isShared): self
    {
        $this->isShared = $isShared;

        return $this;
    }

    public function getOldUid(): ?int
    {
        return $this->oldUid;
    }

    public function setOldUid(?int $oldUid): self
    {
        $this->oldUid = $oldUid;

        return $this;
    }


}
