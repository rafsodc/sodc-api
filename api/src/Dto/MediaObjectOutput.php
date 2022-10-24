<?php

namespace App\Dto;
use Symfony\Component\Serializer\Annotation\Groups;

class MediaObjectOutput
{
    /**
     * @Groups({"mediaobject:read"})
     */
    public $contentUrl;
}
