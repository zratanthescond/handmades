<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\MakeOrderController;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 * collectionOperations={
 * "get"={
 * "normalization_context"={"groups"={"order:collection:read"}}
 * }
 * 
 * },
 * itemOperations={
 * "get"= {
 * "normalization_context"={"groups"={"order:read"}},
 * },
 * "put" = {
 * "denormalization_context"={"groups" = {"order:update"}}
 * },
 * "make_order"={
 * "method": "POST",
 * "controller": MakeOrderController::class,
 * "path": "/orders/make",
 * "read": false,
 * "pagination_enabled": false,
 * "denormalization_context"={"groups" = {"order:write"}},
 * "normalization_context"={"groups" = {"order:read"}},
 * "openapi_context"={
 * "summary"="Create new order",
 * }
 * }
 * }
 * )
 * @ApiFilter(
 * SearchFilter::class,
 * properties={"user.id"} 
 * )
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups({"order:collection:read", "order:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"order:write", "order:read", "order:collection:read"})
     * 
     * */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"order:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order:write", "order:read", "order:collection:read"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrder::class, mappedBy="cOrder", orphanRemoval=true, cascade={"persist", "remove"})
     * @groups({"order:collection:read", "order:read"})
     */
    private $products;


    /**
     * @groups({"order:collection:read", "order:read"})
     */ 
    private $productsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryType::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"order:write", "order:read"})
     */
    private $delivery;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @groups({"order:read", "order:write"})
     */
    private $note;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @groups({"order:collection:read", "order:read", "order:write"})
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=ProductReview::class, mappedBy="uOrder", orphanRemoval=true, cascade={"persist", "remove"})
     * @groups({"order:read", "order:update"})
     */
    private $productReviews;

    /**
     * @ORM\OneToOne(targetEntity=OrderReview::class, mappedBy="cOrder", cascade={"persist", "remove"})
     */
    private $orderReview;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 0;
        $this->products = new ArrayCollection();
        $this->productReviews = new ArrayCollection();
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|ProductOrder[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(ProductOrder $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCOrder($this);
        }

        return $this;
    }

    public function removeProduct(ProductOrder $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCOrder() === $this) {
                $product->setCOrder(null);
            }
        }

        return $this;
    }

    public function getDelivery(): ?DeliveryType
    {
        return $this->delivery;
    }

    public function setDelivery(?DeliveryType $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }


    public function getProductsNumber(): int
    {
        return $this->getProducts()->count();
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|ProductReview[]
     */
    public function getProductReviews(): Collection
    {
        return $this->productReviews;
    }

    public function addProductReview(ProductReview $productReview): self
    {
        if (!$this->productReviews->contains($productReview)) {
            $this->productReviews[] = $productReview;
            $productReview->setUOrder($this);
        }

        return $this;
    }

    public function removeProductReview(ProductReview $productReview): self
    {
        if ($this->productReviews->removeElement($productReview)) {
            // set the owning side to null (unless already changed)
            if ($productReview->getUOrder() === $this) {
                $productReview->setUOrder(null);
            }
        }

        return $this;
    }

    public function getOrderReview(): ?OrderReview
    {
        return $this->orderReview;
    }

    public function setOrderReview(OrderReview $orderReview): self
    {
        // set the owning side of the relation if necessary
        if ($orderReview->getCOrder() !== $this) {
            $orderReview->setCOrder($this);
        }

        $this->orderReview = $orderReview;

        return $this;
    }
}
