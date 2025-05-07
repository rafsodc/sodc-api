<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use CoopTilleuls\ForgotPasswordBundle\Entity\AbstractPasswordToken;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints\Captcha;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: PasswordTokenRepository::class)]
class PasswordToken extends AbstractPasswordToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'uuid')]
    private $user;

    #[Captcha]
    #[Assert\NotBlank]
    private $captcha = "";

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    public function setCaptcha(string $captcha): self
    {
        $this->captcha = $captcha;
        return $this;
    }
}