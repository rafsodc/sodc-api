<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\EventOutput;
use App\Filters\EventDateFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiSubresource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Link;
use Ramsey\Uuid\UuidInterface;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['event:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['event:read']],
            denormalizationContext: ['groups' => ['event:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['event:read']],
            order: ['date' => 'DESC'],
            paginationViaCursor: [['field' => 'date', 'direction' => 'DESC']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['event:read']],
            denormalizationContext: ['groups' => ['event:write']]
        ),
        new Get(
            uriTemplate: '/events/{id}/form',
            security: "is_granted('ROLE_ADMIN')",
            validationContext: ['groups' => ['event:item:getForm']]
        )
    ],
    paginationEnabled: true
)]
#[ApiResource(
    uriTemplate: '/events/{id}/tickets',
    uriVariables: [
        'id' => new Link(fromClass: Event::class, fromProperty: 'tickets', identifiers: ['id'])
    ],
    operations: [new GetCollection(security: "is_granted('ROLE_USER')")],
    normalizationContext: ['groups' => ['event:read']]
)]
#[ApiResource(
    uriTemplate: '/events/{id}/agendas',
    uriVariables: [
        'id' => new Link(fromClass: Event::class, fromProperty: 'agendas', identifiers: ['id'])
    ],
    operations: [new GetCollection(security: "is_granted('ROLE_USER')")],
    normalizationContext: ['groups' => ['event:read']]
)]
#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: '`event`')]
#[ApiFilter(filterClass: EventDateFilter::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['event:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    #[Assert\NotBlank]
    #[ApiProperty(description: 'The event name')]
    private $title;

    #[ORM\Column(type: 'date')]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $date;

    #[ORM\Column(type: 'date')]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $bookingOpen;

    #[ORM\Column(type: 'date')]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $bookingClose;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $venue;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $principalSpeaker;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write', 'event:get', 'event:item:getForm'])]
    private $sponsor;

    #[ORM\OneToMany(targetEntity: TicketType::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read', 'event:get', 'event:item:getForm'])]
    #[ORM\OrderBy(['description' => 'ASC'])]
    private $ticketTypes;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read', 'event:get', 'event:item:getForm'])]
    private $tickets;

    #[ORM\OneToMany(targetEntity: Basket::class, mappedBy: 'event')]
    private $baskets;

    #[Groups(['event:read'])]
    private $isBookingOpen;

    #[ORM\OneToMany(targetEntity: Agenda::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read', 'event:get', 'event:item:getForm'])]
    private $agendas;

    #[ORM\OneToOne(targetEntity: Subscription::class, mappedBy: 'event', cascade: ['persist', 'remove'])]
    #[Groups(['event:read', 'event:get', 'event:item:getForm'])]
    private $subscription;

    public function __construct()
    {
        $this->ticketTypes = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->agendas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBookingOpen(): ?\DateTimeInterface
    {
        return $this->bookingOpen;
    }

    public function setBookingOpen(\DateTimeInterface $bookingOpen): self
    {
        $this->bookingOpen = $bookingOpen;

        return $this;
    }

    public function getBookingClose(): ?\DateTimeInterface
    {
        return $this->bookingClose;
    }

    public function setBookingClose(\DateTimeInterface $bookingClose): self
    {
        $this->bookingClose = $bookingClose;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): self
    {
        $this->venue = $venue;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrincipalSpeaker(): ?string
    {
        return $this->principalSpeaker;
    }

    public function setPrincipalSpeaker(?string $principalSpeaker): self
    {
        $this->principalSpeaker = $principalSpeaker;

        return $this;
    }

    public function getSponsor(): ?string
    {
        return $this->sponsor;
    }

    public function setSponsor(?string $sponsor): self
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * @return Collection|TicketType[]
     */
    public function getTicketTypes(): Collection
    {
        return $this->ticketTypes;
    }

    public function addTicketType(TicketType $ticketType): self
    {
        if (!$this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes[] = $ticketType;
            $ticketType->setEvent($this);
        }

        return $this;
    }

    public function removeTicketType(TicketType $ticketType): self
    {
        if ($this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes->removeElement($ticketType);
            // set the owning side to null (unless already changed)
            if ($ticketType->getEvent() === $this) {
                $ticketType->setEvent(null);
            }
        }

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
            $ticket->setEvent($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getEvent() === $this) {
                $ticket->setEvent(null);
            }
        }

        return $this;
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
            $basket->setEvent($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getEvent() === $this) {
                $basket->setEvent(null);
            }
        }

        return $this;
    }

    public function getIsBookingOpen(): bool
    {
        if ($this->isBookingOpen === null) {
            return false;
        }

        return $this->isBookingOpen;
    }

    public function setIsBookingOpen(bool $isBookingOpen)
    {
        $this->isBookingOpen = $isBookingOpen;
    }

    /**
     * @return Collection|Agenda[]
     */
    public function getAgendas(): Collection
    {
        return $this->agendas;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->agendas->contains($agenda)) {
            $this->agendas[] = $agenda;
            $agenda->setEvent($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getEvent() === $this) {
                $agenda->setEvent(null);
            }
        }

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        // unset the owning side of the relation if necessary
        if ($subscription === null && $this->subscription !== null) {
            $this->subscription->setEvent(null);
        }

        // set the owning side of the relation if necessary
        if ($subscription !== null && $subscription->getEvent() !== $this) {
            $subscription->setEvent($this);
        }

        $this->subscription = $subscription;

        return $this;
    }
}
