<?php

namespace App\Entity;

use App\Repository\DiscountCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext= {"groups" = {"discountCode:read"}},
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 * @ApiFilter(
 * SearchFilter::class,
 * properties={"code": "exact", "promoter.id"}
 * )
 * @ORM\Entity(repositoryClass=DiscountCodeRepository::class)
 * @UniqueEntity("code")
 */
class DiscountCode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"discountCode:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *  min = 4
     * )
     * @Groups({"discountCode:read", "order:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"discountCode:read", "order:read"})
     */
    private $isValid;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"discountCode:read", "order:read"})
     */
    private $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity=Promoter::class, inversedBy="discountCodes")
     * @Groups({"discountCode:read", "order:read"})
     */
    private $promoter;

    /**
     * @ORM\Column(type="integer")
     * @Assert\LessThanOrEqual(100)
     * @Assert\GreaterThan(0)
     * @Groups({"discountCode:read", "order:read"})
     */
    private $percentage;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="discountCode")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=PromoterEarning::class, mappedBy="discountCode", orphanRemoval=true)
     */
    private $promoterEarnings;


    public function __construct()
    {
        $this->isValid = true;

        $this->createdAt = new \DateTimeImmutable();
        $this->orders = new ArrayCollection();
        $this->promoterEarnings = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

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

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setDiscountCode($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getDiscountCode() === $this) {
                $order->setDiscountCode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PromoterEarning[]
     */
    public function getPromoterEarnings(): Collection
    {
        return $this->promoterEarnings;
    }

    public function addPromoterEarning(PromoterEarning $promoterEarning): self
    {
        if (!$this->promoterEarnings->contains($promoterEarning)) {
            $this->promoterEarnings[] = $promoterEarning;
            $promoterEarning->setDiscountCode($this);
        }

        return $this;
    }

    public function removePromoterEarning(PromoterEarning $promoterEarning): self
    {
        if ($this->promoterEarnings->removeElement($promoterEarning)) {
            // set the owning side to null (unless already changed)
            if ($promoterEarning->getDiscountCode() === $this) {
                $promoterEarning->setDiscountCode(null);
            }
        }

        return $this;
    }
}
