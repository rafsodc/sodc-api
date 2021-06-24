<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\IsValidOwner;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\TransactionOutput;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN')",
 *     output=TransactionOutput::CLASS,
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('TRANSACTION_VIEW', object)"},
 *          "patch"={"security"="is_granted('TRANSACTION_EDIT', object)"},
 *          "put",
 *          "delete"
 *     },
 * )
 * @ORM\EntityListeners({"App\Doctrine\TransactionCreateListener"})
 * @ApiFilter(SearchFilter::class, properties={"event": "exact", "owner": "exact"});
 * @ApiFilter(BooleanFilter::class, properties={"isValid"});
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     * @Groups({"transaction:write"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="transactions")
     * @Groups({"transaction:write"})
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="transaction")
     */
    private $tickets;

    /**
     * @ORM\Column(type="float")
     */
    private $amount = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPaid = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"transaction:write"})
     */
    private $isValid = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdDate = new \DateTimeImmutable();
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
            $ticket->setTransaction($this);
            $this->addAmount($ticket->getTicketType()->getPrice());
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getTransaction() === $this) {
                $ticket->setTransaction(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function addAmount(float $amount): self
    {
        $this->amount += $amount;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }
}
