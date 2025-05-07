<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\EventOutput;
use App\Filters\EventDateFilter;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['event:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['event:read']],
            denormalizationContext: ['groups' => ['event:write']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['event:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['event:read']],
            denormalizationContext: ['groups' => ['event:write']]
        )
    ],
    paginationEnabled: false,
    order: ['startDate' => 'DESC']
)]
#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: '`event`')]
#[ApiFilter(filterClass: EventDateFilter::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['event:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event:read', 'event:write'])]
    #[Assert\NotBlank]
    #[ApiProperty(description: 'The event name')]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['event:read', 'event:write'])]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['event:read', 'event:write'])]
    private $startDate;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['event:read', 'event:write'])]
    private $endDate;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event:read', 'event:write'])]
    private $location;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['event:read', 'event:write'])]
    private $isPublished;

    #[ORM\OneToMany(targetEntity: TicketType::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read'])]
    private $ticketTypes;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read'])]
    private $tickets;

    #[ORM\OneToMany(targetEntity: Basket::class, mappedBy: 'event')]
    private $baskets;

    #[Groups(['event:read'])]
    private $isBookingOpen;

    #[ORM\OneToMany(targetEntity: Agenda::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read'])]
    private $agendas;

    #[ORM\OneToOne(targetEntity: Subscription::class, mappedBy: 'event', cascade: ['persist', 'remove'])]
    #[Groups(['event:read'])]
    private $subscription;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageAlt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageTitle;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageDescription;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageCaption;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageCredit;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageCopyright;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageSource;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageThumbnail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageMedium;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageLarge;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXLarge;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXLarge;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXXLarge;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageOriginal;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageMimeType;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageSize;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageWidth;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageHeight;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageAspectRatio;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageOrientation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageColor;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageFormat;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageExtension;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageFilename;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imagePath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageUrlPath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageThumbnailPath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageMediumPath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageLargePath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXLargePath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXLargePath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXXLargePath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageOriginalPath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageThumbnailUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageMediumUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageLargeUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXLargeUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXLargeUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageXXXLargeUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read', 'event:write'])]
    private $imageOriginalUrl;

    public function __construct()
    {
        $this->ticketTypes = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->agendas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return Collection|TicketType[]
     */
    public function getTicketTypes(): Collection
    {
        return $this->ticketTypes;
    }

    public function addTicketType(TicketType $ticketType): self
    {
        if (!$this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes[] = $ticketType;
            $ticketType->setEvent($this);
        }

        return $this;
    }

    public function removeTicketType(TicketType $ticketType): self
    {
        if ($this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes->removeElement($ticketType);
            // set the owning side to null (unless already changed)
            if ($ticketType->getEvent() === $this) {
                $ticketType->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setEvent($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getEvent() === $this) {
                $ticket->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Basket[]
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets[] = $basket;
            $basket->setEvent($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getEvent() === $this) {
                $basket->setEvent(null);
            }
        }

        return $this;
    }

    public function getIsBookingOpen(): bool
    {
        if ($this->isBookingOpen === null) {
            return false;
            //throw new \LogicException('The isBookingOpen field has not been initialized');
        }

        return $this->isBookingOpen;
    }
    public function setIsBookingOpen(bool $isBookingOpen)
    {
        $this->isBookingOpen = $isBookingOpen;
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
        if (!$this->agendas->contains($agenda)) {
            $this->agendas[] = $agenda;
            $agenda->setEvent($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getEvent() === $this) {
                $agenda->setEvent(null);
            }
        }

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        // unset the owning side of the relation if necessary
        if ($subscription === null && $this->subscription !== null) {
            $this->subscription->setEvent(null);
        }

        // set the owning side of the relation if necessary
        if ($subscription !== null && $subscription->getEvent() !== $this) {
            $subscription->setEvent($this);
        }

        $this->subscription = $subscription;

        return $this;
    }

}
