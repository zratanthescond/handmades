<?php

namespace App\Entity;

use App\Repository\RewardPointsHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 * itemOperations={"get"},
 * collectionOperations={"get"}
 * )
 * @ApiFilter(
 * SearchFilter::class,
 * properties={"user.id"}
 * )
 * @ORM\Entity(repositoryClass=RewardPointsHistoryRepository::class)
 */
class RewardPointsHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rewardPointsHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $currentPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $newPoints;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $context;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $operation;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;
    
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCurrentPoints(): ?int
    {
        return $this->currentPoints;
    }

    public function setCurrentPoints(int $currentPoints): self
    {
        $this->currentPoints = $currentPoints;

        return $this;
    }

    public function getNewPoints(): ?int
    {
        return $this->newPoints;
    }

    public function setNewPoints(int $newPoints): self
    {
        $this->newPoints = $newPoints;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }
}
