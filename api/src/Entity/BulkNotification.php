<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BulkNotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
/**
 * @ApiResource(
 *     collectionOperations={
 *          "post"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 * @ORM\Entity(repositoryClass=BulkNotificationRepository::class)
 */
class BulkNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     * @Groups({"bulknotification:write"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"bulknotification:write"})
     */
    private $data = [];

    /**
     * @ORM\Column(type="uuid")
     * @Groups({"bulknotification:write"})
     */
    private $templateId;

    /**
     * @ORM\OneToMany(targetEntity=UserNotification::class, mappedBy="BulkNotification", orphanRemoval=true)
     */
    private $userNotifications;

    public function __construct()
    {
        $this->userNotifications = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
     * @return Collection<int, UserNotification>
     */
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
}
