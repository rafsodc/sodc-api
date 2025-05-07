<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IPGReturnRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints\IPGHash;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['ipgreturn:read']]
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['ipgreturn:read']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: IPGReturnRepository::class)]
#[IPGHash]
class IPGReturn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['ipgreturn:read'])]
    private $id;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $txndate;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['ipgreturn:read'])]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['ipgreturn:read'])]
    private $approvalCode;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['ipgreturn:read'])]
    private $notificationHash;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['ipgreturn:read'])]
    private $status;

    #[ORM\ManyToOne(targetEntity: Transaction::class, inversedBy: 'IPGReturns')]
    #[Groups(['ipgreturn:read'])]
    private $transaction;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $endpointTransactionId;

    #[ORM\Column(type: 'bigint', nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $ipgTransactionId;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $currency;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $total;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['ipgreturn:read'])]
    private $failReason;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['ipgreturn:read'])]
    private $clientReturn;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTxndate(): ?\DateTimeInterface
    {
        return $this->txndate;
    }

    public function setTxndate(?\DateTimeInterface $txndate): self
    {
        $this->txndate = $txndate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getApprovalCode(): ?string
    {
        return $this->approvalCode;
    }

    public function setApprovalCode(string $approvalCode): self
    {
        $this->approvalCode = $approvalCode;

        return $this;
    }

    public function getNotificationHash(): ?string
    {
        return $this->notificationHash;
    }

    public function setNotificationHash(string $notificationHash): self
    {
        $this->notificationHash = $notificationHash;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function isApproved(): ?bool
    {
        return $this->status === "APPROVED";
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        if($status === "APPROVED") {
            $this->getTransaction()->getBasket()->setIsPaid(true);
        }

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;
        
        // unset the owning side of the relation if necessary
        if ($transaction === null && $this->transaction !== null) {
            $this->transaction->removeIPGReturn($this);
        }

        // set the owning side of the relation if necessary
        if ($transaction !== null) {
            $transaction->addIPGReturn($this);
        }

        $this->transaction = $transaction;

        return $this;
    }

    public function getEndpointTransactionId(): ?int
    {
        return $this->endpointTransactionId;
    }

    public function setEndpointTransactionId(?int $endpointTransactionId): self
    {
        $this->endpointTransactionId = $endpointTransactionId;

        return $this;
    }

    public function getIpgTransactionId(): ?int
    {
        return $this->ipgTransactionId;
    }

    public function setIpgTransactionId(?int $ipgTransactionId): self
    {
        $this->ipgTransactionId = $ipgTransactionId;

        return $this;
    }

    public function getCurrency(): ?int
    {
        return $this->currency;
    }

    public function setCurrency(?int $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getFailReason(): ?string
    {
        return $this->failReason;
    }

    public function setFailReason(?string $failReason): self
    {
        $this->failReason = $failReason;

        return $this;
    }

    public function isClientReturn(): ?bool
    {
        return $this->clientReturn;
    }

    public function setClientReturn(bool $clientReturn): self
    {
        $this->clientReturn = $clientReturn;

        return $this;
    }
}
