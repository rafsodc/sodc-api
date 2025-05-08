<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints\Captcha;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['contact:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['contact:read']],
            denormalizationContext: ['groups' => ['contact:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['contact:read']]
        ),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
            normalizationContext: ['groups' => ['contact:read']],
            denormalizationContext: ['groups' => ['contact:write']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['contact:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['contact:write', 'contact:read'])]
    private $subject;

    #[ORM\Column(type: 'text')]
    #[Groups(['contact:read', 'contact:write'])]
    private $message;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['contact:read'])]
    private $createdDate;

    #[Captcha]
    #[Groups(['contact:write'])]
    private $captcha = "";

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setCaptcha(string $captcha): self
    {
        $this->captcha = $captcha;
        return $this;
    }

    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    public function getId(): ?int
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }
}
