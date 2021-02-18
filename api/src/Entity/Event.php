<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(name="`event`")
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN')",
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "put",
 *          "delete"
 *     },
 * )
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"event:write", "event:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Groups({"event:write", "event:read"})
     */
    private $date;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Groups({"event:write", "event:read"})
     */
    private $bookingOpen;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Groups({"event:write", "event:read"})
     */
    private $bookingClose;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"event:write", "event:read"})
     */
    private $venue;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"event:write", "event:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event:write", "event:read"})
     */
    private $princpalSpeaker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event:write", "event:read"})
     */
    private $sponsor;

    /**
     * @ORM\OneToMany(targetEntity=TicketType::class, mappedBy="event", orphanRemoval=true)
     * @Groups({"event:read"})
     */
    private $ticketTypes;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="event", orphanRemoval=true)
     * @Groups({"admin:read"})
     */
    private $tickets;

    public function __construct()
    {
        $this->ticketTypes = new ArrayCollection();
        $this->tickets = new ArrayCollection();
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

    public function getPrincpalSpeaker(): ?string
    {
        return $this->princpalSpeaker;
    }

    public function setPrincpalSpeaker(?string $princpalSpeaker): self
    {
        $this->princpalSpeaker = $princpalSpeaker;

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
}
