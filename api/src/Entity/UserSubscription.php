<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=UserSubscriptionRepository::class)
 */
#[ApiResource]
class UserSubscription
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userSubscriptions")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="userSubscriptions")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $subscription;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getSubscription(): ?Subscriptions
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscriptions $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
