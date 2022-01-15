<?php

namespace App\Entity;

use App\Repository\SiteInfoRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=SiteInfoRepository::class)
 * @ApiResource(
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 */
class SiteInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $instagram;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $MobilePhone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $YoutubeVideo;

    /**
     * @ORM\Column(type="text")
     */
    private $fullAdress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->MobilePhone;
    }

    public function setMobilePhone(string $MobilePhone): self
    {
        $this->MobilePhone = $MobilePhone;

        return $this;
    }

    public function getYoutubeVideo(): ?string
    {
        return $this->YoutubeVideo;
    }

    public function setYoutubeVideo(string $YoutubeVideo): self
    {
        $this->YoutubeVideo = $YoutubeVideo;

        return $this;
    }

    public function getFullAdress(): ?string
    {
        return $this->fullAdress;
    }

    public function setFullAdress(string $fullAdress): self
    {
        $this->fullAdress = $fullAdress;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
