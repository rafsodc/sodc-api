<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\UserSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usersubscription:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usersubscription:read']],
            denormalizationContext: ['groups' => ['usersubscription:write']],
            validationContext: ['groups' => ['usersubscription:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usersubscription:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['usersubscription:read']],
            denormalizationContext: ['groups' => ['usersubscription:write']],
            validationContext: ['groups' => ['usersubscription:write']]
        )
    ],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: UserSubscriptionRepository::class)]
class UserSubscription
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ApiProperty(identifier: true)]
    #[Groups(['usersubscription:read'])]
    private $uuid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSubscriptions')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'uuid')]
    #[Groups(['usersubscription:write', 'usersubscription:read'])]
    private $owner;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: 'userSubscriptions')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'uuid')]
    #[Groups(['owner:read', 'usersubscription:write', 'usersubscription:read'])]
    private $subscription;

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
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
