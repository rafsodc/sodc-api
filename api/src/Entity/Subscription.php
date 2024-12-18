<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "post"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"subscription:write"}
 *           },
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "patch"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"subscription:write"}
 *           },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     attributes={
 *          "pagination_enabled"=false,
 *     }
 * )
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ApiProperty(identifier=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"owner:read", "subscription:read", "subscription:write"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=UserSubscription::class, mappedBy="subscription", orphanRemoval=true)
     */
    private $userSubscriptions;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"subscription:write"})
     */
    private $optout;

    /**
     * @ORM\Column(type="json")
     * @Groups({"subscription:write"})
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=BulkNotification::class, mappedBy="subscription", orphanRemoval=true)
     */
    private $bulkNotifications;

    /**
     * @ORM\OneToOne(targetEntity=Event::class, inversedBy="subscription", cascade={"persist", "remove"})
     * @Groups({"subscription:write"})
     */
    private $event;

    public function __construct()
    {
        $this->userSubscriptions = new ArrayCollection();
        $this->bulkNotifications = new ArrayCollection();
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, UserSubscription>
     */
    public function getUserSubscriptions(): Collection
    {
        return $this->userSubscriptions;
    }

    public function addUserSubscription(UserSubscription $userSubscription): self
    {
        if (!$this->userSubscriptions->contains($userSubscription)) {
            $this->userSubscriptions[] = $userSubscription;
            $userSubscription->setSubscription($this);
        }

        return $this;
    }

    public function removeUserSubscription(UserSubscription $userSubscription): self
    {
        if ($this->userSubscriptions->removeElement($userSubscription)) {
            // set the owning side to null (unless already changed)
            if ($userSubscription->getSubscription() === $this) {
                $userSubscription->setSubscription(null);
            }
        }

        return $this;
    }

    public function isOptout(): ?bool
    {
        return $this->optout;
    }

    public function setOptout(bool $optout): self
    {
        $this->optout = $optout;

        return $this;
    }

    /**
     * @return Collection<int, BulkNotification>
     */
    public function getBulkNotifications(): Collection
    {
        return $this->bulkNotifications;
    }

    public function addBulkNotification(BulkNotification $bulkNotification): self
    {
        if (!$this->bulkNotifications->contains($bulkNotification)) {
            $this->bulkNotifications[] = $bulkNotification;
            $bulkNotification->setSubscription($this);
        }

        return $this;
    }

    public function removeBulkNotification(BulkNotification $bulkNotification): self
    {
        if ($this->bulkNotifications->removeElement($bulkNotification)) {
            // set the owning side to null (unless already changed)
            if ($bulkNotification->getSubscription() === $this) {
                $bulkNotification->setSubscription(null);
            }
        }

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
}
