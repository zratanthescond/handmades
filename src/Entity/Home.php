<?php

namespace App\Entity;

use App\Repository\HomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * collectionOperations={},
 * itemOperations={
 * "get"={"normalization_context"={"groups"={"home:read"}}}
 * }
 * 
 * )
 * @ORM\Entity(repositoryClass=HomeRepository::class)
 */
class Home
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=HomeSlider::class, mappedBy="home", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"home:read"})
     */
    private $sliders;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="home")
     * @Groups({"home:read"})
     */
    private $featuredProducts;

    /**
     * @ORM\OneToMany(targetEntity=HomeSmallBanner::class, mappedBy="home", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"home:read"})
     */
    private $smallBanners;

    public function __construct()
    {
        $this->sliders = new ArrayCollection();
        $this->featuredProducts = new ArrayCollection();
        $this->smallBanners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|HomeSlider[]
     */
    public function getSliders(): Collection
    {
        return $this->sliders;
    }

    public function addSlider(HomeSlider $slider): self
    {
        if (!$this->sliders->contains($slider)) {
            $this->sliders[] = $slider;
            $slider->setHome($this);
        }

        return $this;
    }

    public function removeSlider(HomeSlider $slider): self
    {
        if ($this->sliders->removeElement($slider)) {
            // set the owning side to null (unless already changed)
            if ($slider->getHome() === $this) {
                $slider->setHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getFeaturedProducts(): Collection
    {
        return $this->featuredProducts;
    }

    public function addFeaturedProduct(Product $featuredProduct): self
    {
        if (!$this->featuredProducts->contains($featuredProduct)) {
            $this->featuredProducts[] = $featuredProduct;
            $featuredProduct->setHome($this);
        }

        return $this;
    }

    public function removeFeaturedProduct(Product $featuredProduct): self
    {
        if ($this->featuredProducts->removeElement($featuredProduct)) {
            // set the owning side to null (unless already changed)
            if ($featuredProduct->getHome() === $this) {
                $featuredProduct->setHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HomeSmallBanner[]
     */
    public function getSmallBanners(): Collection
    {
        return $this->smallBanners;
    }

    public function addSmallBanner(HomeSmallBanner $smallBanner): self
    {
        if (!$this->smallBanners->contains($smallBanner)) {
            $this->smallBanners[] = $smallBanner;
            $smallBanner->setHome($this);
        }

        return $this;
    }

    public function removeSmallBanner(HomeSmallBanner $smallBanner): self
    {
        if ($this->smallBanners->removeElement($smallBanner)) {
            // set the owning side to null (unless already changed)
            if ($smallBanner->getHome() === $this) {
                $smallBanner->setHome(null);
            }
        }

        return $this;
    }
}
