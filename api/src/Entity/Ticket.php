<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TicketRepository;
use App\Validator\IsEventOpen;
use App\Validator\IsValidOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\UuidInterface;
use App\Validator\Constraints\TicketPaid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\EventTicketOutput;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('TICKET_VIEW', object)"},
 *          "patch"={"security"="is_granted('TICKET_EDIT', object)"},
 *          "delete"={"security"="is_granted('TICKET_DELETE', object)"},
 *     },
 *     attributes={
 *          "pagination_enabled"=false,
 *          "order"={"owner.lastName", "owner.firstName", "lastname", "firstname"}
 *     },
 *     subresourceOperations={
 *         "api_events_tickets_get_subresource"={
 *             "method"="GET",
 *             "path"="/events/{id}/tickets",
 *             "security"="is_granted('ROLE_USER')",
 *             "normalization_context"={"groups"={"ticket:read"}},
 *         },
 *         "api_users_tickets_get_subresource"={
 *             "method"="GET",
 *             "path"="/users/{uuid}/tickets",
 *             "security"="is_granted('ROLE_ADMIN') or user.getUuid() == request.attributes.get('uuid')",
 *             "normalization_context"={"groups"={"ticket:owner"}},
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\TicketSetOwnerListener"})
 * @ApiFilter(SearchFilter::class, properties={"event": "exact", "owner": "exact"});
 * @TicketPaid
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"ticket:write", "basket:read", "event:item:owner", "ticket:read", "ticket:owner"})
     * @ApiProperty(identifier=true)
     * @Assert\NotBlank()
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @IsValidOwner()
     * @Groups({"ticket:write", "ticket:read", "ticket:owner"})
     * @Assert\NotBlank()
     */
    private $owner;

    // /**
    //  * @ORM\Column(type="uuid", nullable=true)
    //  */
    // private $tempOwnerId;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @IsEventOpen()
     * @Groups({"ticket:write"})
     * @Assert\NotBlank()
     */
    private $event;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read", "basket:read", "ticket:owner"})
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read", "basket:read", "ticket:owner"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read", "basket:read", "ticket:owner"})
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity=TicketType::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ticket:write", "ticket:read", "basket:read", "ticket:owner"})
     * @Assert\NotBlank()
     */
    private $ticketType;

    /**
     * @ORM\Column(type="array")
     */
    private $seatingPreference = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:owner", "admin:read"})
     */
    private $dietary;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"ticket:owner", "admin:read"})
     */
    private $paid;

    /**
     * @ORM\Column(type="date")
     */
    private $createdDate;

    /**
     * @ORM\ManyToMany(targetEntity=Basket::class, mappedBy="tickets")
     * @Groups({"ticket:owner"})
     */
    private $baskets;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @ORM\JoinTable(name="ticket_user",
     *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="uuid")}
     * )
     * @Groups({"ticket:write", "ticket:owner"})
     * @ApiProperty(readableLink=false, writableLink=false)
     */
    private $seatingPreferences;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:write", "ticket:read", "ticket:owner"})
     */
    private $cancelled = false;

    /**
     * @Groups({"admin:read"})
     * @ApiProperty(readableLink=true, writableLink=false)
     */
    public function getSeatingPreferencesDetails(): array
    {
        // This will return the detailed user data
        return $this->seatingPreferences->toArray();
    }


    public function __construct()
    {
        $this->createdDate = new \DateTimeImmutable();
        $this->paid = false;
        $this->baskets = new ArrayCollection();
        $this->seatingPreferences = new ArrayCollection();
        $this->testfield = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTicketType(): ?TicketType
    {
        return $this->ticketType;
    }

    public function setTicketType(?TicketType $ticketType): self
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    public function getSeatingPreference(): ?array
    {
        return $this->seatingPreference;
    }

    public function setSeatingPreference(array $seatingPreference): self
    {
        $this->seatingPreference = $seatingPreference;

        return $this;
    }

    public function getDietary(): ?string
    {
        return $this->dietary;
    }

    public function setDietary(string $dietary): self
    {
        $this->dietary = $dietary;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
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
            $basket->addTickets($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            $basket->removeTickets($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSeatingPreferences(): Collection
    {
        return $this->seatingPreferences;
    }

    // /**
    //  * @return Collection|User[]
    //  * @Groups({"event_ticket:read"})
    //  * @SerializedName("seatingPreferences")
    //  * We already have the serialized name seatingPreferences, but that's only referenced in a ticket:read/write call.  For an event_ticket:read call, we want
    //  * to display the fullname, and not the IRI.
    //  */
    // public function getSeatingPreferenceNames(): Collection
    // {
    //     return $this->seatingPreferences;
    // }

    public function addSeatingPreference(User $seatingPreference): self
    {
        if (!$this->seatingPreferences->contains($seatingPreference)) {
            $this->seatingPreferences[] = $seatingPreference;
        }

        return $this;
    }

    public function removeSeatingPreference(User $seatingPreference): self
    {
        $this->seatingPreferences->removeElement($seatingPreference);

        return $this;
    }

    public function isCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }


}
