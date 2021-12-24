<?php

namespace App\Entity;

use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\ApiOperation\ProductCategory\NavigationController;


/**
 * @ApiResource(
 * normalizationContext= {"groups" = {"category:read", "product:read"}},
 * collectionOperations={
 * "get",
 * "navigation"={
 * "method": "GET",
 * "controller": NavigationController::class,
 * "path": "/product_categories/navigation"
 * }
 * },
 * itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass=ProductCategoryRepository::class)
 */
class ProductCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read", "category:read", "brand:read", "home:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "category:read", "brand:read", "home:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"category:read"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $products;


    /**
     * @Groups({"category:read"})
     */
    private $productsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=ProductCategory::class, inversedBy="productCategories")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=ProductCategory::class, mappedBy="parent")
     */
    private $productCategories;
    
    /**
     * this is used only for to add subitems within API
     * @Groups({"category:read"})
     */

    public $subItems = [];

    public function __toString()
    {
        return $this->title;
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productCategories = new ArrayCollection();
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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

 
    public function getProductsNumber()
    {
        return $this->getProducts()->count();
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getProductCategories(): Collection
    {
        return $this->productCategories;
    }

    public function addProductCategory(self $productCategory): self
    {
        if (!$this->productCategories->contains($productCategory)) {
            $this->productCategories[] = $productCategory;
            $productCategory->setParent($this);
        }

        return $this;
    }

    public function removeProductCategory(self $productCategory): self
    {
        if ($this->productCategories->removeElement($productCategory)) {
            // set the owning side to null (unless already changed)
            if ($productCategory->getParent() === $this) {
                $productCategory->setParent(null);
            }
        }

        return $this;
    }
    
}
