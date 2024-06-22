<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NotifyMessageRepository;
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
 * @ORM\Entity(repositoryClass=NotifyMessageRepository::class)
 */
class NotifyMessage
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
     * @Groups({"notifymessage:write"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"notifymessage:write"})
     */
    private $data = [];

    /**
     * @ORM\Column(type="uuid")
     * @Groups({"notifymessage:write"})
     */
    private $templateId;

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
}
