<?php

namespace App\Entity;

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\IsValidOwner;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\TransactionOutput;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('BASKET_VIEW', object)",
            normalizationContext: ['groups' => ['basket:read']]
        ),
        new Patch(
            security: "is_granted('BASKET_EDIT', object)",
            normalizationContext: ['groups' => ['basket:read']],
            denormalizationContext: ['groups' => ['basket:write']]
        ),
        new Delete(security: "is_granted('BASKET_DELETE', object)"),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['basket:read']]
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['basket:read']],
            denormalizationContext: ['groups' => ['basket:write']]
        )
    ],
    paginationEnabled: false
)]
#[ApiResource(
    uriTemplate: '/users/{uuid}/baskets.{_format}',
    uriVariables: [
        'uuid' => new Link(
            fromClass: \App\Entity\User::class,
            identifiers: ['uuid']
        )
    ],
    status: 200,
    operations: [new GetCollection()]
)]
#[ORM\Entity(repositoryClass: BasketRepository::class)]
#[ORM\EntityListeners(['App\Doctrine\BasketListener'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['event' => 'exact', 'owner' => 'exact'])]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['basket:read'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'baskets')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'uuid')]
    #[IsValidOwner]
    #[Groups(['basket:read', 'basket:write'])]
    private $owner;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'baskets')]
    #[Groups(['basket:read', 'basket:write'])]
    private $event;

    #[ORM\Column(type: 'float')]
    #[Groups(['basket:read'])]
    private $amount = 0;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['basket:read'])]
    private $createdDate;

    #[ORM\OneToOne(targetEntity: Transaction::class, inversedBy: 'basket', cascade: ['persist', 'remove'])]
    #[Groups(['basket:read'])]
    private $transaction;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['basket:read'])]
    private $isTransaction;

    #[ORM\ManyToMany(targetEntity: Ticket::class, inversedBy: 'baskets')]
    #[Groups(['basket:read', 'basket:write'])]
    private $tickets;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['basket:read', 'basket:write'])]
    private $isPaid = false;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    #[Groups(['basket:owner'])]
    #[ApiProperty(
        openapiContext: [
            'description' => 'URL to download the invoice for this basket\'s transaction',
            'type' => 'string',
            'example' => '/invoice/1'
        ]
    )]
    private ?string $invoiceUrl = null;

    /**
     * Get the URL for the associated transaction invoice.
     * If no transaction is associated, this returns null.
     */
    public function getInvoiceUrl(): ?string
    {
        return $this->transaction ? sprintf('/invoices/%d', $this->transaction->getId()) : null;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
