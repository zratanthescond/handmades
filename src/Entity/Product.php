<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;

/**
 * @ApiResource(
 * order={"createdAt": "DESC"},
 * normalizationContext= {"groups" = {"product:read"}},
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 * 
 * @ApiFilter(
 * SearchFilter::class,
 * properties={
 * "category.id": "exact", 
 * "title": "partial", 
 * "type.slug": "exact",
 * "brand.country": "exact",
 * "users"
 * }
 * )
 * 
 * @ApiFilter(
 * RangeFilter::class,
 * properties={"id"}
 * )
 * 
 * @ApiFilter(
 * DateFilter::class,
 * properties={"discount.expireAt"}
 * )
 * 
 * @ApiFilter(
 * ExistsFilter::class,
 * properties={"discount"},
 * 
 * )
 * 
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read", "brand:read", "user:read", "home:read", "order:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "brand:read", "order:read", "home:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"product:read", "brand:read"})
     */
    private $description;


    /**
     * @Groups({"product:read", "brand:read"})
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=ProductImage::class, mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"product:read"})
     */
    private $images;

    /**
     * @Groups({"product:read", "brand:read", "home:read", "order:read"})
     */

    private $thumbnail;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=ProductCategory::class, inversedBy="products")
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $qty;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products")
     * @Groups({"product:read", "home:read"})
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrder::class, mappedBy="product", orphanRemoval=true)
     */
    private $productOrders;

    /**
     * @ORM\OneToOne(targetEntity=ProductDiscount::class, mappedBy="product", cascade={"persist", "remove"})
     * @Groups({"product:read", "brand:read", "home:read"})
     */
    private $discount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read", "brand:read"})
     */
    private $ref;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"product:read", "brand:read"})
     */
    private $isApprovisionnable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read", "brand:read"})
     */
    private $originCountry;

    /**
     * @ORM\OneToMany(targetEntity=ProductInfo::class, mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $infos;

    /**
     * @Groups({"product:read"})
     */
    private $rewardPoints = [];

    /**
     * @ORM\OneToMany(targetEntity=ProductStockSubscription::class, mappedBy="product", orphanRemoval=true)
     */
    private $stockSubscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=ProductType::class, inversedBy="products")
     */
    private $type;

     /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="wishList")
     * @Groups({"product:read"})
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Home::class, inversedBy="featuredProducts")
     */
    private $home;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max = 170)
     */
    private $metaDescription;

    /**
     * @ORM\OneToMany(targetEntity=ProductReview::class, mappedBy="product", orphanRemoval=true)
     */
    private $reviews;

    public function __construct()
    {
       $this->createdAt = new DateTimeImmutable();
       $this->images = new ArrayCollection();
       $this->productOrders = new ArrayCollection();
       $this->infos = new ArrayCollection();
       $this->stockSubscriptions = new ArrayCollection();
       $this->users = new ArrayCollection();
       $this->reviews = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|ProductImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }

    public function setCategory(?ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getThumbnail()
    {
        if($this->images->isEmpty() === false) {

             return $this->images->first()->getName();
        }

        return "preview400.png";
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(?int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection|ProductOrder[]
     */
    public function getProductOrders(): Collection
    {
        return $this->productOrders;
    }

    public function addProductOrder(ProductOrder $productOrder): self
    {
        if (!$this->productOrders->contains($productOrder)) {
            $this->productOrders[] = $productOrder;
            $productOrder->setProduct($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->productOrders->removeElement($productOrder)) {
            // set the owning side to null (unless already changed)
            if ($productOrder->getProduct() === $this) {
                $productOrder->setProduct(null);
            }
        }

        return $this;
    }

    public function getDiscount(): ?ProductDiscount
    {
        return $this->discount;
    }

    public function setDiscount(ProductDiscount $discount): self
    {
        // set the owning side of the relation if necessary
        if ($discount->getProduct() !== $this) {
            $discount->setProduct($this);
        }

        $this->discount = $discount;

        return $this;
    }



    /**
     * Get the value of shortDescription
     */ 
    public function getShortDescription()
    {
        return substr($this->description, 0, 50). "...";
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getIsApprovisionnable(): ?bool
    {
        return $this->isApprovisionnable;
    }

    public function setIsApprovisionnable(?bool $isApprovisionnable): self
    {
        $this->isApprovisionnable = $isApprovisionnable;

        return $this;
    }

    public function getOriginCountry(): ?string
    {
        return $this->originCountry;
    }

    public function setOriginCountry(?string $originCountry): self
    {
        $this->originCountry = $originCountry;

        return $this;
    }

    /**
     * @return Collection|ProductInfo[]
     */
    public function getInfos(): Collection
    {
        return $this->infos;
    }

    public function addInfo(ProductInfo $info): self
    {
        if (!$this->infos->contains($info)) {
            $this->infos[] = $info;
            $info->setProduct($this);
        }

        return $this;
    }

    public function removeInfo(ProductInfo $info): self
    {
        if ($this->infos->removeElement($info)) {
            // set the owning side to null (unless already changed)
            if ($info->getProduct() === $this) {
                $info->setProduct(null);
            }
        }

        return $this;
    }

 
    public function getRewardPoints()
    {
        return $this->rewardPoints;
    }

    public function setRewardPoints($rewardPoints)
    {
        $this->rewardPoints = $rewardPoints;

        return $this;
    }

    /**
     * @return Collection|ProductStockSubscription[]
     */
    public function getStockSubscriptions(): Collection
    {
        return $this->stockSubscriptions;
    }

    public function addStockSubscription(ProductStockSubscription $stockSubscription): self
    {
        if (!$this->stockSubscriptions->contains($stockSubscription)) {
            $this->stockSubscriptions[] = $stockSubscription;
            $stockSubscription->setProduct($this);
        }

        return $this;
    }

    public function removeStockSubscription(ProductStockSubscription $stockSubscription): self
    {
        if ($this->stockSubscriptions->removeElement($stockSubscription)) {
            // set the owning side to null (unless already changed)
            if ($stockSubscription->getProduct() === $this) {
                $stockSubscription->setProduct(null);
            }
        }

        return $this;
    }

    public function getType(): ?ProductType
    {
        return $this->type;
    }

    public function setType(?ProductType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of users
     */ 
    public function getUsers()
    {
        return $this->users;
    }

    public function getHome(): ?Home
    {
        return $this->home;
    }

    public function setHome(?Home $home): self
    {
        $this->home = $home;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return Collection|ProductReview[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(ProductReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(ProductReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }
}
