<?php

namespace App\Dto;
use Symfony\Component\Serializer\Annotation\Groups;

class TransactionOutput
{
    /**
     * @Groups({"transaction:read"})
     */
    public $id;

    /**
     * @Groups({"transaction:read"})
     */
    public $owner;

    /**
     * @Groups({"transaction:read"})
     */
    public $event;

    /**
     * @Groups({"transaction:read"})
     */
    public $tickets;

    /**
     * @Groups({"transaction:read"})
     */
    public $amount;

    /**
     * @Groups({"transaction:read"})
     */
    public $isPaid;

    /**
     * @Groups({"transaction:read"})
     */
    public $ipg;

    /**
     * @Groups({"transaction:read"})
     */
    public $isValid;


}
