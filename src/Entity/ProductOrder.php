<?php

namespace App\Entity;

use App\Repository\ProductOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductOrderRepository::class)
 */
class ProductOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups({"order:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productOrders")
     * @ORM\JoinColumn(nullable=false)
     * @groups({"order:read"})
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     * @groups({"order:read"})
     */
    private $qty;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

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
}
