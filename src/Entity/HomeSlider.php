<?php

namespace App\Entity;

use App\Repository\HomeSliderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HomeSliderRepository::class)
 * @Vich\Uploadable()
 */
class HomeSlider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"home:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"home:read"})
     */
    private $image;

     /**
     * @Vich\UploadableField(mapping="img_upload", fileNameProperty="image")
     * @Assert\File(maxSize="10M")
     */

    private $imageFile;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Home::class, inversedBy="sliders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $home;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"home:read"})
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
