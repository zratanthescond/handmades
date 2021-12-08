<?php

namespace App\Entity;

use App\Repository\AramexPickUpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AcmeAssert;

/**
 * @ORM\Entity(repositoryClass=AramexPickUpRepository::class)
 */
class AramexPickUp
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=AramexShipement::class, mappedBy="aramexPickUp", orphanRemoval=true, cascade={"persist"})
     * @Assert\Count(min=1, minMessage="Chaque PickUp doit contenir 1 shippement ou plus.")
     */
    private $shippements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pickUpId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guid;

    /**
     * @Assert\GreaterThan("now")
     * @Assert\LessThan("+ 7 days")
     * @AcmeAssert\AramexPickUpDate
     * @ORM\Column(type="datetime")
     * 
     */
    private $readyTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("now")
     * @Assert\GreaterThan(propertyPath="readyTime", message="Cette date doit être supérieure au readyTime")
     * @AcmeAssert\AramexPickUpDate
     * 
     */
    private $lastPickupTime;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->shippements = new ArrayCollection();
        $this->readyTime = new \DateTime();
        $this->lastPickupTime = (new \DateTime())->setTime(19, 00, 00);
    
    }

    public function __toString()
    {
        return "pickup";
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

    /**
     * @return Collection|AramexShipement[]
     */
    public function getShippements(): Collection
    {
        return $this->shippements;
    }

    public function addShippement(AramexShipement $shippement): self
    {
        if (!$this->shippements->contains($shippement)) {
            $this->shippements[] = $shippement;
            $shippement->setAramexPickUp($this);
        }

        return $this;
    }

    public function removeShippement(AramexShipement $shippement): self
    {
        if ($this->shippements->removeElement($shippement)) {
            // set the owning side to null (unless already changed)
            if ($shippement->getAramexPickUp() === $this) {
                $shippement->setAramexPickUp(null);
            }
        }

        return $this;
    }

    public function getPickUpId(): ?string
    {
        return $this->pickUpId;
    }

    public function setPickUpId(?string $pickUpId): self
    {
        $this->pickUpId = $pickUpId;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getReadyTime(): ?\DateTimeInterface
    {
        return $this->readyTime;
    }

    public function setReadyTime(\DateTimeInterface $readyTime): self
    {
        $this->readyTime = $readyTime;

        return $this;
    }

    public function getLastPickupTime(): ?\DateTimeInterface
    {
        return $this->lastPickupTime;
    }

    public function setLastPickupTime(\DateTimeInterface $lastPickupTime): self
    {
        $this->lastPickupTime = $lastPickupTime;

        return $this;
    }


}
