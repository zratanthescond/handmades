<?php

namespace App\Entity;

use App\Repository\DiscountCodeRepository;
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
     * @Groups({"discountCode:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"discountCode:read"})
     */
    private $isValid;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"discountCode:read"})
     */
    private $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity=Promoter::class, inversedBy="discountCodes")
     * @Groups({"discountCode:read"})
     */
    private $promoter;

    /**
     * @ORM\Column(type="integer")
     * @Assert\LessThanOrEqual(100)
     * @Assert\GreaterThan(0)
     * @Groups({"discountCode:read"})
     */
    private $percentage;


    public function __construct()
    {
        $this->isValid = true;

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
}
