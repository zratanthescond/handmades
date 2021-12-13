<?php

namespace App\Entity;

use App\Repository\ParrainageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiOperation\Parrainage\NewParrainageController;

/**
 * @ApiResource(
 * itemOperations={"get"},
 * collectionOperations={
 * "get",
 * "new"={
 * "method": "POST",
 * "path": "/parrainages/new",
 * "controller": NewParrainageController::class
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=ParrainageRepository::class)
 * @UniqueEntity("beneficiaryEmail")
 */
class Parrainage
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
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $fromUser;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $beneficiaryEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $beneficiaryFirstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $beneficiaryLastName;

    private $beneficiaryFullName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRewarded;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $isRewardedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        $this->isRewarded = false;
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

    public function getFromUser(): ?User
    {
        return $this->fromUser;
    }

    public function setFromUser(?User $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    public function getBeneficiaryEmail(): ?string
    {
        return $this->beneficiaryEmail;
    }

    public function setBeneficiaryEmail(string $beneficiaryEmail): self
    {
        $this->beneficiaryEmail = $beneficiaryEmail;

        return $this;
    }

    public function getBeneficiaryFirstName(): ?string
    {
        return $this->beneficiaryFirstName;
    }

    public function setBeneficiaryFirstName(string $beneficiaryFirstName): self
    {
        $this->beneficiaryFirstName = $beneficiaryFirstName;

        return $this;
    }

    public function getBeneficiaryLastName(): ?string
    {
        return $this->beneficiaryLastName;
    }

    public function setBeneficiaryLastName(string $beneficiaryLastName): self
    {
        $this->beneficiaryLastName = $beneficiaryLastName;

        return $this;
    }

    public function getIsRewarded(): ?bool
    {
        return $this->isRewarded;
    }

    public function setIsRewarded(bool $isRewarded): self
    {
        $this->isRewarded = $isRewarded;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIsRewardedAt(): ?\DateTimeImmutable
    {
        return $this->isRewardedAt;
    }

    public function setIsRewardedAt(?\DateTimeImmutable $isRewardedAt): self
    {
        $this->isRewardedAt = $isRewardedAt;

        return $this;
    }


    public function getBeneficiaryFullName()
    {
        return $this->beneficiaryFirstName . " " . $this->beneficiaryLastName;
    }
}
