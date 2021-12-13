<?php

namespace App\Entity;

use App\Repository\BlogCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * order={"createdAt": "DESC"},
 * normalizationContext= {"groups" = {"blogComment:read"}}
 * )
 * @ApiFilter(
 * SearchFilter::class,
 * properties={"blog.id": "exact"}
 * )
 * @ApiFilter(
 * BooleanFilter::class,
 * properties={"isValidated"}
 * )
 * 
 * )
 * @ORM\Entity(repositoryClass=BlogCommentRepository::class)
 */
class BlogComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"blogComment:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"blogComment:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogComments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"blogComment:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Blog::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blog;

    /**
     * @ORM\Column(type="text")
     * @Groups({"blogComment:read"})
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"blogComment:read"})
     */
    private $isValidated;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        $this->isValidated = false;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
