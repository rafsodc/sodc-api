<?php

namespace App\Entity;

use App\Repository\NotifyMessageUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotifyMessageUserRepository::class)
 */
class NotifyMessageUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifyMessageUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=NotifyMessage::class, inversedBy="notifyMessageUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notifyMessage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="json")
     */
    private $data = [];

    public function getId(): ?int
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

    public function getNotifyMessage(): ?NotifyMessage
    {
        return $this->notifyMessage;
    }

    public function setNotifyMessage(?NotifyMessage $notifyMessage): self
    {
        $this->notifyMessage = $notifyMessage;

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
}
