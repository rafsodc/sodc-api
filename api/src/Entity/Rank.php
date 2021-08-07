<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RankRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RankRepository::class)
 */
#[ApiResource]
class Rank
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
    private $rank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }
}
