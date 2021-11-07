<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SpeakerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $biography;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     */
    private $photograph;

    /**
     * @ORM\ManyToMany(targetEntity=Agendum::class, mappedBy="speakers")
     */
    private $agendums;

    public function __construct()
    {
        $this->agendums = new ArrayCollection();
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

    public function getPhotograph(): ?Media
    {
        return $this->photograph;
    }

    public function setPhotograph(?Media $photograph): self
    {
        $this->photograph = $photograph;

        return $this;
    }

    /**
     * @return Collection|Agendum[]
     */
    public function getAgendums(): Collection
    {
        return $this->agendums;
    }

    public function addAgendum(Agendum $agendum): self
    {
        if (!$this->agendums->contains($agendum)) {
            $this->agendums[] = $agendum;
            $agendum->addSpeaker($this);
        }

        return $this;
    }

    public function removeAgendum(Agendum $agendum): self
    {
        if ($this->agendums->removeElement($agendum)) {
            $agendum->removeSpeaker($this);
        }

        return $this;
    }
}
