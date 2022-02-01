<?php

namespace App\Entity;

use App\Repository\AramexTrackingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=AramexTrackingRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class AramexTracking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"trackings:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"trackings:read", "order:read", "order:update"})
     */
    private $data = [];

    /**
     * @ORM\OneToOne(targetEntity=AramexShipement::class, inversedBy="tracking", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $shippement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"trackings:read"})
     */
    private $UpdateCode;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"trackings:read"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $waybillNumber;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
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

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getShippement(): ?AramexShipement
    {
        return $this->shippement;
    }

    public function setShippement(AramexShipement $shippement): self
    {
        $this->shippement = $shippement;

        return $this;
    }

    public function getUpdateCode(): ?string
    {
        return $this->UpdateCode;
    }

    public function setUpdateCode(?string $UpdateCode): self
    {
        $this->UpdateCode = $UpdateCode;

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

    public function getWaybillNumber(): ?string
    {
        return $this->waybillNumber;
    }

    public function setWaybillNumber(string $waybillNumber): self
    {
        $this->waybillNumber = $waybillNumber;

        return $this;
    }
}
