<?php

namespace App\Entity;

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AgendaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
            normalizationContext: ['groups' => ['agenda:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['agenda:read']],
            denormalizationContext: ['groups' => ['agenda:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
            normalizationContext: ['groups' => ['agenda:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['agenda:read']],
            denormalizationContext: ['groups' => ['agenda:write']]
        )
    ],
    paginationEnabled: false,
    order: ['start' => 'ASC']
)]
#[ApiResource(
    uriTemplate: '/events/{id}/agendas.{_format}',
    uriVariables: [
        'id' => new Link(
            fromClass: \App\Entity\Event::class,
            identifiers: ['id']
        )
    ],
    status: 200,
    operations: [new GetCollection()]
)]
#[ORM\Entity(repositoryClass: AgendaRepository::class)]
class Agenda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['agenda:read'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'agendas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $event;

    #[ORM\ManyToMany(targetEntity: Speaker::class, inversedBy: 'agendas')]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $speakers;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $start;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['agenda:read', 'agenda:write'])]
    private $finish;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['agenda:write'])]
    private $hidden;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['agenda:read', 'agenda:write'])]
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
