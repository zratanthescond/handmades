<?php

namespace App\Entity;

use App\Repository\OrderReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderReviewRepository::class)
 */
class OrderReview
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $deliveryRating;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $suggestion;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="orderReview", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $cOrder;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isValidated = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryRating(): ?int
    {
        return $this->deliveryRating;
    }

    public function setDeliveryRating(int $deliveryRating): self
    {
        $this->deliveryRating = $deliveryRating;

        return $this;
    }

    public function getSuggestion(): ?string
    {
        return $this->suggestion;
    }

    public function setSuggestion(?string $suggestion): self
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    public function getCOrder(): ?Order
    {
        return $this->cOrder;
    }

    public function setCOrder(Order $cOrder): self
    {
        $this->cOrder = $cOrder;

        return $this;
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

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
