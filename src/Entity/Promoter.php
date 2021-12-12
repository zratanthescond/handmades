<?php

namespace App\Entity;

use App\Repository\PromoterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PromoterRepository::class)
 * @UniqueEntity("email")
 */
class Promoter implements UserInterface, PasswordAuthenticatedUserInterface
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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

      /**
     * @SerializedName("password")
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    
    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = [];

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
       
         $roles[] = 'ROLE_PROMOTER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

       /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {

        $this->plainPassword = null;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
