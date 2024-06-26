<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Filters\UserFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Validator\Constraints\Captcha;
use App\Controller\ApproveUserController;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "validation_groups"={"user:post"}
 *          },
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('USER_VIEW', object)"},
 *          "patch"={
 *              "security"="is_granted('USER_EDIT', object)",
 *              "validation_groups"={"user:item:write"},
 *          },
 *          "approve"={
 *              "method"="POST",
 *              "path"="/users/{id}/approve",
 *              "controller"=ApproveUserController::class,
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"user:approve"}
 *          },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     subresourceOperations={
 *          "api_users_approve_patch_subresource"= {
 *              "method"="PATCH",
 *              "security"="is_granted('ROLE_ADMIN')"
 *          }
 *     }
 * )
 * @UniqueEntity(fields={"email"})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact"});
 * @ApiFilter(BooleanFilter::class, properties={"isMember"})
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
     * @Groups({"owner:read", "user:write"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"admin:write"})
     */
    private $roles = [];

    /**
     * @Groups("user:write")
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"user:post"})
     */
    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"owner:read", "user:write"})
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
     * @Groups({"owner:read", "user:write"})
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $postNominals;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"owner:read", "admin:read", "user:write"})
     */
    private $serviceNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"owner:read", "admin:read", "user:write"})
     */
    private $modnetEmail;

    /**
     * @ORM\ManyToOne(targetEntity=Rank::class, inversedBy="users")
     * @Groups({"user:read", "user:write"})
     */
    private $rank;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"owner:read", "admin:read", "user:write"})
     */
    private $workDetails;

    /**
     * @Groups({"owner:read", "user:write"})
     * @ORM\Column(type="boolean", options={"default":true})
     * @Assert\Type("bool")
     * @Assert\NotNull
     */
    private $isShared = true;

    /**
     * @Groups({"admin:read", "user:item:write"})
     * @ORM\Column(type="boolean", options={"default":true})
     * @Assert\Type("bool")
     * @Assert\NotNull
     */
    private $isSubscribed = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isMember = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldUid;

    /**
     * @Captcha
     * @Groups({"user:post"})
     */
    private $captcha = "";

    /**
     * @Groups("ticket:read", "event_ticket:read")
     */
    private $fullName;

    /**
     * @ORM\OneToMany(targetEntity=UserNotification::class, mappedBy="owner", orphanRemoval=true)
     */
    private $userNotifications;


    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->userNotification = new ArrayCollection();
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
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_unique($roles);

        if (in_array('ROLE_MEMBER', $roles)) {
            $this->setIsMember(true);
        }

        return $this;
    }

    public function addRole($role): self
    {
        $roles = $this->roles;
        $roles[] = $role;
        $this->setRoles($roles);
        return $this;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (in_array($role, $this->roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasExclusionRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (strpos($role, '!') === 0) {
                $exclusionRole = substr($role, 1);
                if (in_array($exclusionRole, $this->roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hasAnyRoleWithExclusions(array $roles): bool
    {
        return $this->hasAnyRole($roles) && !$this->hasExclusionRole($roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
     * @param mixed $plainPassword
     * @return self
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

    public function getIsSubscribed(): ?bool
    {
        return $this->isSubscribed;
    }

    public function setIsSubscribed(bool $isSubscribed): self
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    public function getIsMember(): ?bool
    {
        return $this->isMember;
    }

    public function setIsMember(bool $isMember): self
    {
        $this->isMember = $isMember;

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

    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    public function setCaptcha(string $captcha): self
    {
        $this->captcha = $captcha;
        return $this;
    }

    public function getFullName(): string
    {
        return "{$this->getLastName()}, {$this->getFirstName()}";
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return Collection<int, UserNotification>
     */
    public function getuserNotifications(): Collection
    {
        return $this->userNotifications;
    }

    public function addUserNotification(UserNotification $userNotification): self
    {
        if (!$this->userNotifications->contains($userNotification)) {
            $this->userNotifications[] = $userNotification;
            $userNotification->setOwner($this);
        }

        return $this;
    }

    public function removeUserNotification(UserNotification $userNotification): self
    {
        if ($this->userNotifications->removeElement($userNotification)) {
            // set the owning side to null (unless already changed)
            if ($userNotification->getOwner() === $this) {
                $userNotification->setOwner(null);
            }
        }

        return $this;
    }
}
