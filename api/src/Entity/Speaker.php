<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\SpeakerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\MediaObject;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "patch"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     attributes={
 *          "pagination_enabled"=false,
 *          "order"={"lastname", "firstname"}
 *     }
 * )
 * @ORM\Entity(repositoryClass=SpeakerRepository::class)
 */
class Speaker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"speaker:read", "speaker:write"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"speaker:read", "speaker:write"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"speaker:read", "speaker:write"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"speaker:read", "speaker:write", "agenda:read"})
     */
    private $biography;

    /**
     * @ORM\OneToOne(targetEntity=MediaObject::class, cascade={"persist", "remove"})
     * @ApiProperty(iri="http://schema.org/image", readableLink=true, writableLink=false)
     * @Groups({"speaker:read", "speaker:write", "agenda:read"})
     */
    private $photograph;

    /**
     * @ORM\ManyToMany(targetEntity=Agenda::class, mappedBy="speakers")
     */
    private $agendas;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"speaker:read", "speaker:write"})
     */
    private $postnominals;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"speaker:read", "speaker:write", "agenda:read"})
     */
    private $position;

    public function __construct()
    {
        $this->agendas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getPhotograph(): ?MediaObject
    {
        return $this->photograph;
    }

    public function setPhotograph(?MediaObject $photograph): self
    {
        $this->photograph = $photograph;

        return $this;
    }

    /**
     * @return Collection|Agenda[]
     */
    public function getAgendas(): Collection
    {
        return $this->agendas;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->agendas->contains($agendas)) {
            $this->agendas[] = $agendas;
            $agendas->addSpeaker($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->removeElement($agenda)) {
            $agenda->removeSpeaker($this);
        }

        return $this;
    }

    /**
     * @Groups({"agenda:read"})
     */
    public function getFullname(): string {
        return sprintf("%s %s %s %s", $this->title, $this->firstname, $this->lastname, $this->postnominals);
    }

    /**
     * @Groups({"agenda:read"})
     */
    public function getHasBio(): bool {
        return !empty($this->biography);
    }

    public function getPostnominals(): ?string
    {
        return $this->postnominals;
    }

    public function setPostnominals(string $postnominals): self
    {
        $this->postnominals = $postnominals;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }
}
