<?php

namespace App\Entity;

use App\Repository\PayementTransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PayementTransactionRepository::class)
 */
class PayementTransaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups({"order:collection:read", "order:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups({"order:collection:read", "order:read"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups({"order:collection:read", "order:read"})
     */
    private $ref;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="payementTransaction", cascade={"persist", "remove"})
     */
    private $cOrder;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @groups({"order:collection:read", "order:read"})
     */
    private $data = [];

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {

        $this->createdAt = new \DateTimeImmutable();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getCOrder(): ?Order
    {
        return $this->cOrder;
    }

    public function setCOrder(?Order $cOrder): self
    {
        $this->cOrder = $cOrder;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
