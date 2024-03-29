<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints\Captcha;
use Symfony\Component\Serializer\Annotation\Groups;

/**
  * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "patch"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 * )
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contact:write", "contact:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contact:write", "contact:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contact:write", "contact:read"})
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     * @Groups({"contact:write", "contact:read"})
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    public function __construct()
    {
        $this->createdDate = new \DateTimeImmutable();
    }

    /**
     * @Captcha
     * @Groups({"contact:write"})
     */
    private $captcha = "";

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
