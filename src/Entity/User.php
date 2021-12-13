<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ApiResource(
 * collectionOperations={"get", "post"},
 * itemOperations={"put", 
 * "get"={"normalization_context"={"groups"={"user:read"}}},
 * }
 * )
 * @UniqueEntity("email")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"order:read", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"order:read", "user:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @SerializedName("password")
     */
    
     private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"order:read", "user:read", "blogComment:read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"order:read", "user:read", "blogComment:read"})
     */
    private $lastName;

    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"order:read", "user:read"})
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=UserAddress::class, mappedBy="user", orphanRemoval=true)
     */
    private $addresses;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"order:read", "user:read"})
     */
    private $birthDay;

    /**
     * @Groups({"order:read", "user:read"})
     */
    private $defaultAddress;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $rewardPoints;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     * @Groups({"user:read"})
     */
    private $wishList;

    /**
     * @ORM\OneToOne(targetEntity=ResetPassword::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $resetPassword;

    /**
     * @ORM\OneToMany(targetEntity=RewardPointsHistory::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $rewardPointsHistories;

    /**
     * @ORM\OneToMany(targetEntity=BlogComment::class, mappedBy="user", orphanRemoval=true)
     */
    private $blogComments;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->rewardPoints = 0;
        $this->wishList = new ArrayCollection();
        $this->rewardPointsHistories = new ArrayCollection();
        $this->blogComments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
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
       // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        
        $this->plainPassword = null;
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAddress[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(UserAddress $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(UserAddress $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    
    public function getDefaultAddress($str = true)
    {
        $addresses = $this->getAddresses();

        if(!$addresses->isEmpty()) {

              $defaultAddresses = array_filter($addresses->toArray(), function(UserAddress $a){

                    return $a->getIsDefault();
              });

              if(!empty($defaultAddresses)) {

                 $key = array_key_first($defaultAddresses);

                 return $defaultAddresses[$key]->getFullAddress();
              }
        }
       
        return "NA";
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

     public function getRewardPoints(): ?int
     {
         return $this->rewardPoints;
     }

     public function setRewardPoints(int $rewardPoints): self
     {
         $this->rewardPoints = $rewardPoints;

         return $this;
     }

     /**
      * @return Collection|Product[]
      */
     public function getWishList(): Collection
     {
         return $this->wishList;
     }

     public function addWishList(Product $wishList): void
     {
         if (!$this->wishList->contains($wishList)) {
             $this->wishList->add($wishList);
         }

         //return $this;
     }

     public function removeWishList(Product $wishList): self
     {
         $this->wishList->removeElement($wishList);

         return $this;
     }

     public function getResetPassword(): ?ResetPassword
     {
         return $this->resetPassword;
     }

     public function setResetPassword(ResetPassword $resetPassword): self
     {
         // set the owning side of the relation if necessary
         if ($resetPassword->getUser() !== $this) {
             $resetPassword->setUser($this);
         }

         $this->resetPassword = $resetPassword;

         return $this;
     }

     /**
      * @return Collection|RewardPointsHistory[]
      */
     public function getRewardPointsHistories(): Collection
     {
         return $this->rewardPointsHistories;
     }

     public function addRewardPointsHistory(RewardPointsHistory $rewardPointsHistory): self
     {
         if (!$this->rewardPointsHistories->contains($rewardPointsHistory)) {
             $this->rewardPointsHistories[] = $rewardPointsHistory;
             $rewardPointsHistory->setUser($this);
         }

         return $this;
     }

     public function removeRewardPointsHistory(RewardPointsHistory $rewardPointsHistory): self
     {
         if ($this->rewardPointsHistories->removeElement($rewardPointsHistory)) {
             // set the owning side to null (unless already changed)
             if ($rewardPointsHistory->getUser() === $this) {
                 $rewardPointsHistory->setUser(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|BlogComment[]
      */
     public function getBlogComments(): Collection
     {
         return $this->blogComments;
     }

     public function addBlogComment(BlogComment $blogComment): self
     {
         if (!$this->blogComments->contains($blogComment)) {
             $this->blogComments[] = $blogComment;
             $blogComment->setUser($this);
         }

         return $this;
     }

     public function removeBlogComment(BlogComment $blogComment): self
     {
         if ($this->blogComments->removeElement($blogComment)) {
             // set the owning side to null (unless already changed)
             if ($blogComment->getUser() === $this) {
                 $blogComment->setUser(null);
             }
         }

         return $this;
     }
}
