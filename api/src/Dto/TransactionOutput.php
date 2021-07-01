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
    public $status;

    /**
     * @Groups({"transaction:read"})
     */
    public $isExpired;

    /**
     * @Groups({"transaction:read"})
     */
    public $basket;

    /**
     * @Groups({"transaction:read"})
     */
    public $ipg;



}
