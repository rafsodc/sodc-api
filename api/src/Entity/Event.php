<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\EventOutput;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filters\EventDateFilter;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(name="`event`")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "getForm"={
 *              "method"="GET",
 *              "path"="/events/{id}/form",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"event:item:getForm"}
 *          },
 *          "patch"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 * )
 * @ApiFilter(EventDateFilter::class)
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"event:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\Column(type="date")
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @Assert\NotBlank()
     */
    private $bookingOpen;

    /**
     * @ORM\Column(type="date")
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @Assert\NotBlank()
     */
    private $bookingClose;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @Assert\NotBlank()
     */
    private $venue;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     */
    private $principalSpeaker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     */
    private $sponsor;

    /**
     * @ORM\OneToMany(targetEntity=TicketType::class, mappedBy="event", orphanRemoval=true)
     * @Groups({"event:write", "event:get", "event:item:getForm"})
     * @ORM\OrderBy({"description" = "ASC"})
     */
    private $ticketTypes;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="event", orphanRemoval=true)
     * @Groups({"event:write", "event:get"})
     * @ApiSubresource()
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity=Basket::class, mappedBy="event")
     * @Groups({"event:write", "event:get"})
     */
    private $baskets;

    /**
     * @Groups({"event:get"})
     */
    private $isBookingOpen;

    /**
     * @ORM\OneToMany(targetEntity=Agenda::class, mappedBy="event", orphanRemoval=true)
     * @ApiSubresource()
     */
    private $agendas;

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
        if (!$this->baskets->contains($baskets)) {
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
            //throw new \LogicException('The isBookingOpen field has not been initialized');
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

}
