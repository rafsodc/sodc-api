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
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN')",
 *     output=TransactionOutput::CLASS,
 *     collectionOperations={
 *          "get"={"security"="is_granted('TRANSACTION_GET', object)"},
 *          "post"={"security"="is_granted('TRANSACTION_POST', object)"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('TRANSACTION_GET', object)"},
 *          "patch"={"security"="is_granted('TRANSACTION_PATCH', object)"},
 *          "put",
 *          "delete"
 *     },
 * )
 * @ORM\EntityListeners({"App\Doctrine\TransactionCreateListener"})
 * @ApiFilter(SearchFilter::class, properties={"event": "exact", "owner": "exact"});
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
     * @ORM\Column(type="boolean")
     */
    private $paid = false;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
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

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }
}
