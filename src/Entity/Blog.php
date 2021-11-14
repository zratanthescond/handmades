<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource(
 * normalizationContext= {"groups" = {"blog:read"}},
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass=BlogRepository::class)
 * @Vich\Uploadable()
 */
class Blog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"blog:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"blog:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"blog:read"})
     */
    private $content;

   
    /**
     * @Groups({"blog:read"})
     */

    private $desc;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"blog:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"blog:read"})
     */
    private $image;

     /**
     * @Vich\UploadableField(mapping="img_upload", fileNameProperty="image")
     * @Assert\File(maxSize="10M")
     */

    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=BlogCategory::class, inversedBy="blogs")
     *  @Groups({"blog:read"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"blog:read"})
     */
    private $slug;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

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

    public function getImageFile()
    {
         return $this->imageFile;
    }

    public function setImageFile(?File $imageFile)
    {
         $this->imageFile = $imageFile;

         if($imageFile) {

            $this->updatedAt = new \DateTimeImmutable();
         }

         return $this;
    }

    /**
     * Get the value of desc
     */ 
    public function getDesc()
    {
        $substr = substr($this->content, 0, 150) . "...";

        return strip_tags($substr);
    }
}
