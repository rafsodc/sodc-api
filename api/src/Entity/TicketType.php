<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TicketTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"order"={"description"}},
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "patch"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 * )
 * @ORM\Entity(repositoryClass=TicketTypeRepository::class)
 */
class TicketType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @ApiProperty(identifier=true)
     * @Assert\NotBlank()
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tickettype:write", "tickettype:read", "basket:read", "event:item:read","event:item:getForm"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="ticketTypes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"tickettype:write", "tickettype:read"})
     */
    private $event;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tickettype:write", "tickettype:read", "ticket:read", "event_ticket:read", "event:item:getForm"})
     */
    private $symposium;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tickettype:write", "tickettype:read", "ticket:read", "event_ticket:read", "event:item:getForm"})
     */
    private $dinner;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tickettype:write", "tickettype:read", "ticket:read", "event_ticket:read", "event:item:getForm"})
     */
    private $serving;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tickettype:write", "tickettype:read", "event:item:getForm"})
     */
    private $student;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tickettype:write", "tickettype:read", "ticket:read", "event_ticket:read", "event:item:getForm"})
     */
    private $guest;

    /**
     * @ORM\Column(type="float")
     * @Groups({"tickettype:write", "tickettype:read", "event:item:read", "basket:read", "ticket:read", "event_ticket:read", "event:item:getForm"})
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="ticketType")
     */
    private $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSymposium(): ?bool
    {
        return $this->symposium;
    }

    public function setSymposium(bool $symposium): self
    {
        $this->symposium = $symposium;

        return $this;
    }

    public function getDinner(): ?bool
    {
        return $this->dinner;
    }

    public function setDinner(bool $dinner): self
    {
        $this->dinner = $dinner;

        return $this;
    }

    public function getServing(): ?bool
    {
        return $this->serving;
    }

    public function setServing(bool $serving): self
    {
        $this->serving = $serving;

        return $this;
    }

    public function getStudent(): ?bool
    {
        return $this->student;
    }

    public function setStudent(bool $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getGuest(): ?bool
    {
        return $this->guest;
    }

    public function setGuest(bool $guest): self
    {
        $this->guest = $guest;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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
            $ticket->setTicketType($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getTicketType() === $this) {
                $ticket->setTicketType(null);
            }
        }

        return $this;
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
}
