<?php
// api/src/Entity/MediaObject.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateMediaObjectAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Dto\MediaObjectOutput;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    output: MediaObjectOutput::class,
    iri: 'http://schema.org/MediaObject',
    normalizationContext: [
        'groups' => ['mediaobject:read']
    ],
    collectionOperations: [
        'post' => [
            'controller' => CreateMediaObjectAction::class,
            'deserialize' => false,
            'security' => "is_granted('ROLE_USER')",
            'validation_groups' => ['Default', 'mediaobject:create'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'get'
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('ROLE_USER')"
        ]
    ]
)]
class MediaObject
{
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    protected $id;

    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[Groups(['mediaobject:read', 'agenda:read'])]
    public $contentUrl;

    #[Assert\NotNull(groups: ['mediaobject:create'])]
    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath', mimeType: 'mediaMimeType', size: 'mediaSize')]
    public $file;

    #[ORM\Column(nullable: true)]
    public $filePath;

    #[ORM\Column(nullable: true)]
    public $mediaMimeType;

    #[ORM\Column(nullable: true)]
    public $mediaSize;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}