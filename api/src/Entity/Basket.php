<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BasketRepository;
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
 * @ORM\Entity(repositoryClass=BasketRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('BASKET_VIEW', object)"},
 *          "patch"={"security"="is_granted('BASKET_EDIT', object)"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 * )
 * @ORM\EntityListeners({"App\Doctrine\BasketListener"})
 * @ApiFilter(SearchFilter::class, properties={"event": "exact", "owner": "exact"});
 */
class Basket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"basket:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="baskets")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     * @Groups({"basket:write", "basket:read"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="baskets")
     * @Groups({"basket:write", "basket:read"})
     */
    private $event;

    /**
     * @ORM\Column(type="float")
     * @Groups({"basket:read"})
     */
    private $amount = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\OneToOne(targetEntity=Transaction::class, inversedBy="basket", cascade={"persist", "remove"})
     * @Groups({"basket:read"})
     */
    private $transaction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"basket:read"})
     */
    private $isTransaction;

    /**
     * @ORM\ManyToMany(targetEntity=Ticket::class, inversedBy="baskets")
     * @Groups({"basket:read"})
     */
    private $tickets;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"basket:read"})
     */
    private $isPaid = false;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdDate = new \DateTimeImmutable();
        $this->tickets_new = new ArrayCollection();
        $this->isTransaction = false;
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
            $this->addAmount($ticket->getTicketType()->getPrice());
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            $this->subtractAmount($ticket->getTicketType()->getPrice());
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

    public function subtractAmount(float $amount): self
    {
        $this->amount -= $amount;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        // unset the owning side of the relation if necessary
        if ($transaction === null && $this->transaction !== null) {
            $this->transaction->setBasket(null);
        }

        // set the owning side of the relation if necessary
        if ($transaction !== null && $transaction->getBasket() !== $this) {
            $transaction->setBasket($this);
        }

        $this->transaction = $transaction;

        return $this;
    }

    public function getIsTransaction(): ?bool
    {
        return $this->isTransaction;
    }

    public function setIsTransaction(bool $isTransaction): self
    {
        $this->isTransaction = $isTransaction;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        if($isPaid) {
            foreach($this->getTickets() as $ticket) {
                $ticket->setPaid(true);
            }
        }

        return $this;
    }


}
