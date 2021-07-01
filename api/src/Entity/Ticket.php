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

/**
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN')",
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('TICKET_VIEW', object)"},
 *          "patch"={"security"="is_granted('TICKET_EDIT', object)"},
 *          "put",
 *          "delete"
 *     },
 * )
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\TicketSetOwnerListener"})
 * @ApiFilter(SearchFilter::class, properties={"event": "exact", "owner": "exact"});
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"event:item:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @IsEventOpen()
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $event;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity=TicketType::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $ticketType;

    /**
     * @ORM\Column(type="array")
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $seatingPreference = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write", "ticket:read"})
     */
    private $dietary;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"ticket:read"})
     */
    private $paid;

    /**
     * @ORM\Column(type="date")
     */
    private $createdDate;

    /**
     * @ORM\ManyToMany(targetEntity=Basket::class, mappedBy="tickets")
     * @Groups({"ticket:read"})
     */
    private $baskets;

    public function __construct()
    {
        $this->createdDate = new \DateTimeImmutable();
        $this->paid = false;
        $this->baskets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
