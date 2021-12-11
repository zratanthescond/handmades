<?php

namespace App\Entity;

use App\Repository\PromoterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PromoterRepository::class)
 * @UniqueEntity("email")
 */
class Promoter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"discountCode:read"})
     * @Groups({"discountCode:read", "order:read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"discountCode:read", "order:read"})
     */
    private $lastName;

    /**
     * @Groups({"discountCode:read"})
     */

    private $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=DiscountCode::class, mappedBy="promoter")
     */
    private $discountCodes;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"discountCode:read"})
     */
    private $isActif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"discountCode:read"})
     */
    private $alias;

    public function __construct()
    {
        $this->discountCodes = new ArrayCollection();

        $this->createdAt = new \DateTimeImmutable();

        $this->isActif = true;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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
     * @return Collection|DiscountCode[]
     */
    public function getDiscountCodes(): Collection
    {
        return $this->discountCodes;
    }

    public function addDiscountCode(DiscountCode $discountCode): self
    {
        if (!$this->discountCodes->contains($discountCode)) {
            $this->discountCodes[] = $discountCode;
            $discountCode->setPromoter($this);
        }

        return $this;
    }

    public function removeDiscountCode(DiscountCode $discountCode): self
    {
        if ($this->discountCodes->removeElement($discountCode)) {
            // set the owning side to null (unless already changed)
            if ($discountCode->getPromoter() === $this) {
                $discountCode->setPromoter(null);
            }
        }

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

  
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
