<?php

namespace App\Entity;

use App\Repository\PromoterEarningRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromoterEarningRepository::class)
 */
class PromoterEarning
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
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="promoterEarnings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cOrder;

    /**
     * @ORM\Column(type="float")
     */
    private $percent;

    /**
     * @ORM\ManyToOne(targetEntity=Promoter::class, inversedBy="earnings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $promoter;

    /**
     * @ORM\Column(type="float")
     */
    private $income;

    /**
     * @ORM\ManyToOne(targetEntity=DiscountCode::class, inversedBy="promoterEarnings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $discountCode;


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

    public function getCOrder(): ?Order
    {
        return $this->cOrder;
    }

    public function setCOrder(?Order $cOrder): self
    {
        $this->cOrder = $cOrder;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getPromoter(): ?Promoter
    {
        return $this->promoter;
    }

    public function setPromoter(?Promoter $promoter): self
    {
        $this->promoter = $promoter;

        return $this;
    }

    public function getIncome(): ?float
    {
        return $this->income;
    }

    public function setIncome(float $income): self
    {
        $this->income = $income;

        return $this;
    }

    public function getDiscountCode(): ?DiscountCode
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?DiscountCode $discountCode): self
    {
        $this->discountCode = $discountCode;

        return $this;
    }
}
