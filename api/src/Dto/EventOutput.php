<?php

namespace App\Dto;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

class EventOutput
{
    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $title;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $date;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $bookingOpen;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $bookingClose;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $venue;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $description;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $principalSpeaker;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $sponsor;

    /**
     * @var string
     * @Groups({"event:read"})
     */
    public $ticketTypes;

    /**
     * @var bool
     * @Groups({"event:read"})
     */
    public $isBookingOpen;

    /**
     * @var bool
     * @Groups({"event:read"})
     */
    public $tickets;
}
