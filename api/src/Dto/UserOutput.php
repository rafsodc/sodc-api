<?php

namespace App\Dto;
use Symfony\Component\Serializer\Annotation\Groups;

class UserOutput
{
    /**
     * @var string
     * @Groups({"user:read"})
     */
    public $username;

    /**
     * @var string
     * @Groups({"user:read"})
     */
    public $email;

    /**
     * @var string
     * @Groups({"admin:read", "owner:read"})
     */
    public $phoneNumber;

    /**
     * @var bool
     * @Groups({"user:read"})
     */
    public $isMe;
}
