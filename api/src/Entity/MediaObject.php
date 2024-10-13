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

/**
 * @ORM\Entity
 * @ApiResource(
 *     output=MediaObjectOutput::CLASS,
 *     iri="http://schema.org/MediaObject",
 *     normalizationContext={
 *         "groups"={"mediaobject:read"}
 *     },
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateMediaObjectAction::class,
 *             "deserialize"=false,
 *             "security"="is_granted('ROLE_USER')",
 *             "validation_groups"={"Default", "mediaobject:create"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"
 *     },
 *     itemOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_USER')",
 *          }
 *     }
 * )
 * @Vich\Uploadable
 */
class MediaObject
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({"mediaobject:read", "agenda:read"})
     */
    public $contentUrl;

    /**
     * @var File|null
     *
     * @Assert\NotNull(groups={"mediaobject:create"})
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filePath", mimeType="mediaMimeType", size="mediaSize")
     */
    public $file;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    public $filePath;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    public $mediaMimeType;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
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