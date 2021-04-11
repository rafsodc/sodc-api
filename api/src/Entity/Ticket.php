<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TicketRepository;
use App\Validator\IsEventOpen;
use App\Validator\IsValidOwner;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN')",
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "patch"={"security"="is_granted('TICKET_EDIT', object)"},
 *          "put",
 *          "delete"
 *     },
 * )
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\TicketSetOwnerListener"})
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     * @Groups({"ticket:write"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @IsEventOpen()
     * @Groups({"ticket:write"})
     */
    private $event;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write"})
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write"})
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity=TicketType::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ticket:write"})
     */
    private $ticketType;

    /**
     * @ORM\Column(type="array")
     * @Groups({"ticket:write"})
     */
    private $seatingPreference = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket:write"})
     */
    private $dietary;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paid;

    /**
     * @ORM\Column(type="date")
     */
    private $createdDate;

    public function __construct()
    {
        $this->createdDate = new \DateTimeImmutable();
        $this->paid = false;
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
}
