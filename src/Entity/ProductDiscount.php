<?php

namespace App\Entity;

use App\Repository\ProductDiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource(
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass=ProductDiscountRepository::class)
 */
class ProductDiscount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"product:read", "brand:read", "home:read", "order:read"})
     */
    private $pourcentage;

  
    /**
     * @Groups({"product:read", "brand:read", "home:read", "order:read"})
     */

    private $newPrice;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $expireAt;


    /**
     * @Groups({"product:read", "brand:read", "home:read"})
     * 
     * */ 
    
     private $isExpired;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="discount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourcentage(): ?int
    {
        return $this->pourcentage;
    }

    public function setPourcentage(int $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

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

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

 
    public function getNewPrice()
    {  
        $price = $this->product->getPrice();

        $discountValue = $price * $this->pourcentage / 100;

        return round($price - $discountValue);
    }

    public function setNewPrice($newPrice): self
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    
     public function getIsExpired()
     {

          if(!$this->expireAt) {

            return false;
          }

         return $this->expireAt <= new \DateTime("now");
     }

    
     public function setIsExpired($isExpired): self
     {
          $this->isExpired = $isExpired;

          return $this;
     }
}
