<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\IsBasketFree;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\TransactionOutput;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    output: TransactionOutput::class,
    operations: [
        new Get(
            security: "is_granted('TRANSACTION_VIEW', object)",
            normalizationContext: ['groups' => ['transaction:read']]
        ),
        new Patch(
            security: "is_granted('TRANSACTION_EDIT', object)",
            normalizationContext: ['groups' => ['transaction:read']],
            denormalizationContext: ['groups' => ['transaction:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['transaction:read']]
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['transaction:read']],
            denormalizationContext: ['groups' => ['transaction:write']]
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['event' => 'exact', 'owner' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['isValid'])]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['transaction:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['transaction:read'])]
    private $status;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['transaction:read'])]
    private $createdAt;

    #[ORM\OneToOne(targetEntity: Basket::class, mappedBy: 'transaction', cascade: ['persist', 'remove'])]
    #[Groups(['transaction:read', 'transaction:write'])]
    #[IsBasketFree]
    private $basket;

    private $isExpired;

    #[ORM\OneToMany(targetEntity: IPGReturn::class, mappedBy: 'transaction')]
    #[Groups(['transaction:read'])]
    private $IPGReturns;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->status = "Active";
        $this->IPGReturns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getBasket(): ?Basket
    {
        //dd($this->basket);
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;
        $basket->setTransaction($this);
        return $this;
    }

    public function getIsExpired(): bool
    {
        $expiry = new \DateTime('-1 hour');
        return ($this->getCreatedAt() < $expiry);
    }

    /**
     * @return Collection|IPGReturn[]
     */
    public function getIPGReturns(): Collection
    {
        return $this->IPGReturns;
    }

    public function addIPGReturn(IPGReturn $iPGReturn): self
    {
        if (!$this->IPGReturns->contains($iPGReturn)) {
            $this->IPGReturns[] = $iPGReturn;
            $iPGReturn->setTransaction($this);
        }

        return $this;
    }

    public function removeIPGReturn(IPGReturn $iPGReturn): self
    {
        if ($this->IPGReturns->removeElement($iPGReturn)) {
            // set the owning side to null (unless already changed)
            if ($iPGReturn->getTransaction() === $this) {
                $iPGReturn->setTransaction(null);
            }
        }

        return $this;
    }

}
