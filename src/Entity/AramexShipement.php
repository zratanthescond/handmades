<?php

namespace App\Entity;

use App\Repository\AramexShipementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AramexShipementRepository::class)
 */
class AramexShipement
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
    private $trackingId;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="aramexShipement", cascade={"persist", "remove"})
     */
    private $clientOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=AramexPickUp::class, inversedBy="shippements")
     */
    private $aramexPickUp;

    private $isPickedUp;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        $this->status = "in progress";
    }

    public function __toString()
    {
        return $this->trackingId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }

    public function setTrackingId(string $trackingId): self
    {
        $this->trackingId = $trackingId;

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

    public function getClientOrder(): ?Order
    {
        return $this->clientOrder;
    }

    public function setClientOrder(?Order $clientOrder): self
    {
        $this->clientOrder = $clientOrder;

        return $this;
    }

    public function getAttachement(): ?string
    {
        return $this->attachement;
    }

    public function setAttachement(?string $attachement): self
    {
        $this->attachement = $attachement;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getIsPickedUp(): bool
    {
        return !!$this->getAramexPickUp();
    }

   public function getAramexPickUp(): ?AramexPickUp
   {
       return $this->aramexPickUp;
   }

   public function setAramexPickUp(?AramexPickUp $aramexPickUp): self
   {
       $this->aramexPickUp = $aramexPickUp;

       return $this;
   }
}
