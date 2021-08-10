<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\IsBasketFree;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\TransactionOutput;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Validator\Constraints as Assert;

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
 *          "delete"
 *     },
 * )
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
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=Basket::class, mappedBy="transaction", cascade={"persist", "remove"})
     * @Groups({"transaction:write"})
     * @IsBasketFree
     */
    private $basket;

    private $isExpired;

    /**
     * @ORM\OneToMany(targetEntity=IPGReturn::class, mappedBy="transaction")
     */
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
