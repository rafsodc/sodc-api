<?php

namespace App\Entity;

use App\Repository\NotificationReturnRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationReturnRepository::class)
 */
class NotificationReturn
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="uuid", nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sentTo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $completedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $notificationType;

    /**
     * @ORM\Column(type="uuid")
     */
    private $templateId;

    /**
     * @ORM\Column(type="integer")
     */
    private $templateVersion;

    /**
     * @ORM\ManyToOne(targetEntity=UserNotification::class, inversedBy="notificationReturns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userNotification;

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
}
