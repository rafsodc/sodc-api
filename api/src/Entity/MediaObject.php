<?php
// api/src/Entity/MediaObject.php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\CreateMediaObjectAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Dto\MediaObjectOutput;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    output: MediaObjectOutput::class,
    operations: [
        new Post(
            controller: CreateMediaObjectAction::class,
            deserialize: false,
            security: "is_granted('ROLE_USER')",
            validationContext: ['groups' => ['Default', 'mediaobject:create']],
            normalizationContext: ['groups' => ['mediaobject:read']],
            openapiContext: [
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
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['mediaobject:read']]
        ),
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['mediaobject:read']]
        )
    ]
)]
class MediaObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['mediaobject:read'])]
    protected $id;

    #[ApiProperty(types: ['https://schema.org/name'])]
    #[Groups(['mediaobject:read', 'agenda:read'])]
    public $contentUrl;

    #[Assert\NotNull(groups: ['mediaobject:create'])]
    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath', mimeType: 'mediaMimeType', size: 'mediaSize')]
    public $file;

    #[ORM\Column(nullable: true)]
    #[Groups(['mediaobject:read'])]
    public $filePath;

    #[ORM\Column(nullable: true)]
    #[Groups(['mediaobject:read'])]
    public $mediaMimeType;

    #[ORM\Column(nullable: true)]
    #[Groups(['mediaobject:read'])]
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