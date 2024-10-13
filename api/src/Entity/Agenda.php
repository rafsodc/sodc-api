<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AgendaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
 *          "order"={"start"}
 *     },
 *     subresourceOperations={
 *          "api_events_agendas_get_subresource"= {
 *              "method"="GET",
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"agenda:read"}}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=AgendaRepository::class)
 */
class Agenda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="agendas")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"agenda:write"})
     */
    private $event;

    /**
     * @ORM\ManyToMany(targetEntity=Speaker::class, inversedBy="agendas")
     * @Groups({"agenda:read", "agenda:write"})
     */
    private $speakers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"agenda:read", "agenda:write"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"agenda:write", "agenda:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"agenda:read", "agenda:write"})
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"agenda:read", "agenda:write"})
     */
    private $finish;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"agenda:write"})
     */
    private $hidden;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"agenda:read", "agenda:write"})
     */
    private $break;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection|Speaker[]
     */
    public function getSpeakers(): Collection
    {
        return $this->speakers;
    }

    public function addSpeaker(Speaker $speaker): self
    {
        if (!$this->speakers->contains($speaker)) {
            $this->speakers[] = $speaker;
        }

        return $this;
    }

    public function removeSpeaker(Speaker $speaker): self
    {
        $this->speakers->removeElement($speaker);

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeInterface
    {
        return $this->finish;
    }

    public function setFinish(\DateTimeInterface $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getBreak(): ?bool
    {
        return $this->break;
    }

    public function setBreak(bool $break): self
    {
        $this->break = $break;

        return $this;
    }
}
