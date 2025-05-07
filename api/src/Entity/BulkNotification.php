<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\BulkNotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['bulknotification:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['bulknotification:read']],
            denormalizationContext: ['groups' => ['bulknotification:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['bulknotification:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['bulknotification:read']],
            denormalizationContext: ['groups' => ['bulknotification:write']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: BulkNotificationRepository::class)]
class BulkNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['bulknotification:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['bulknotification:read', 'bulknotification:write'])]
    private $name;

    #[ORM\Column(type: 'text')]
    #[Groups(['bulknotification:read', 'bulknotification:write'])]
    private $message;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['bulknotification:read'])]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['bulknotification:read', 'bulknotification:write'])]
    private $sent = false;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: 'bulkNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['bulknotification:read', 'bulknotification:write'])]
    private $subscription;

    #[ORM\OneToMany(targetEntity: UserNotification::class, mappedBy: 'bulkNotification', orphanRemoval: true)]
    #[Groups(['bulknotification:read'])]
    private $userNotifications;

    public function __construct()
    {
        $this->userNotifications = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getUserNotifications(): Collection
    {
        return $this->userNotifications;
    }

    public function addUserNotifications(UserNotification $userNotification): self
    {
        if (!$this->userNotifications->contains($userNotification)) {
            $this->userNotifications[] = $userNotification;
            $userNotification->setBulkNotification($this);
        }

        return $this;
    }

    public function removeUserNotifications(UserNotification $userNotification): self
    {
        if ($this->userNotifications->removeElement($userNotification)) {
            // set the owning side to null (unless already changed)
            if ($userNotification->getBulkNotification() === $this) {
                $userNotification->setBulkNotification(null);
            }
        }

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
