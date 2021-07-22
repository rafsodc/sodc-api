<?php
namespace App\Entity;

use CoopTilleuls\ForgotPasswordBundle\Entity\AbstractPasswordToken;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints\Captcha;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class PasswordToken extends AbstractPasswordToken
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
    * @Captcha
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
}