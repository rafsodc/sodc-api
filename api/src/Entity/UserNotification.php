<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserNotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usernotification:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usernotification:read']],
            denormalizationContext: ['groups' => ['usernotification:write']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: UserNotificationRepository::class)]
class UserNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['usernotification:read'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userNotifications')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'uuid')]
    #[Groups(['usernotification:write', 'usernotification:read', 'bulknotification:read'])]
    private $user;

    // /**
    //  * @ORM\Column(type="uuid", nullable=true)
    //  */
    // private $tempUserId;

    #[ORM\ManyToOne(targetEntity: BulkNotification::class, inversedBy: 'userNotifications')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['usernotification:read'])]
    private $bulkNotification;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['usernotification:read'])]
    private $sent = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['usernotification:read'])]
    private $status;

    #[ORM\Column(type: 'json')]
    #[Groups(['usernotification:write', 'usernotification:read'])]
    private $data = [];

    #[ORM\Column(type: 'uuid')]
    #[Groups(['usernotification:write'])]
    private $templateId;

    #[ORM\OneToMany(targetEntity: NotificationReturn::class, mappedBy: 'userNotification', orphanRemoval: true)]
    #[Groups(['usernotification:read'])]
    private $notificationReturns;

    public function __construct()
    {
        $this->notificationReturns = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBulkNotification(): ?BulkNotification
    {
        return $this->bulkNotification;
    }

    public function setBulkNotification(?BulkNotification $bulkNotification): self
    {
        $this->bulkNotification = $bulkNotification;

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    public function getTemplateId()
    {
        return $this->templateId;
    }

    public function setTemplateId($templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * @return Collection<int, NotificationReturn>
     */
    public function getNotificationReturns(): Collection
    {
        return $this->notificationReturns;
    }

    public function addNotificationReturn(NotificationReturn $notificationReturn): self
    {
        if (!$this->notificationReturns->contains($notificationReturn)) {
            $this->notificationReturns[] = $notificationReturn;
            $notificationReturn->setUserNotification($this);
        }

        return $this;
    }

    public function removeNotificationReturn(NotificationReturn $notificationReturn): self
    {
        if ($this->notificationReturns->removeElement($notificationReturn)) {
            // set the owning side to null (unless already changed)
            if ($notificationReturn->getUserNotification() === $this) {
                $notificationReturn->setUserNotification(null);
            }
        }

        return $this;
    }
}
