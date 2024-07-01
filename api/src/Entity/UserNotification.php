<?php

namespace App\Entity;

use App\Repository\NotifyUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "post"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 * @ORM\Entity(repositoryClass=NotifyUserRepository::class)
 */
class UserNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userNotifications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"usernotification:write"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=BulkNotification::class, inversedBy="userNotifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bulkNotification;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $sent = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="json")
     * @Groups({"usernotification:write"})
     */
    private $data = [];

    /**
     * @ORM\Column(type="uuid")
     * @Groups({"usernotification:write"})
     */
    private $templateId;

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
        return $this->BulkNotification;
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
}
