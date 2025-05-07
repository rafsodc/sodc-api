<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\NotificationReturnRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['notificationreturn:read']]
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['notificationreturn:read']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: NotificationReturnRepository::class)]
class NotificationReturn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['notificationreturn:read'])]
    private $id;

    #[ORM\Column(type: 'uuid', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $reference;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['notificationreturn:read'])]
    private $sentTo;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['notificationreturn:read'])]
    private $status;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['notificationreturn:read'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $completedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $sentAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['notificationreturn:read'])]
    private $notificationType;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['notificationreturn:read'])]
    private $templateId;

    #[ORM\Column(type: 'integer')]
    #[Groups(['notificationreturn:read'])]
    private $templateVersion;

    #[ORM\ManyToOne(targetEntity: UserNotification::class, inversedBy: 'notificationReturns')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notificationreturn:read'])]
    private $userNotification;

    #[ORM\ManyToOne(targetEntity: Transaction::class, inversedBy: 'notificationReturns')]
    #[Groups(['notificationreturn:read'])]
    private $transaction;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $endpointTransactionId;

    #[ORM\Column(type: 'bigint', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $ipgTransactionId;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $currency;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $total;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $failReason;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['notificationreturn:read'])]
    private $clientReturn;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['notificationreturn:read'])]
    private $error;

    #[ORM\Column(type: 'json')]
    #[Groups(['notificationreturn:read'])]
    private $data = [];

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getSentTo(): ?string
    {
        return $this->sentTo;
    }

    public function setSentTo(string $sentTo): self
    {
        $this->sentTo = $sentTo;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getNotificationType(): ?string
    {
        return $this->notificationType;
    }

    public function setNotificationType(string $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    public function getTemplateId()
    {
        return $this->templateId;
    }

    public function setTemplateId($templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getTemplateVersion(): ?int
    {
        return $this->templateVersion;
    }

    public function setTemplateVersion(int $templateVersion): self
    {
        $this->templateVersion = $templateVersion;

        return $this;
    }

    public function getUserNotification(): ?UserNotification
    {
        return $this->userNotification;
    }

    public function setUserNotification(?UserNotification $userNotification): self
    {
        $this->userNotification = $userNotification;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
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

    public function getClientReturn(): ?bool
    {
        return $this->clientReturn;
    }

    public function setClientReturn(bool $clientReturn): self
    {
        $this->clientReturn = $clientReturn;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
