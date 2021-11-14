<?php

namespace App\Entity;

use App\Repository\HelpSectionRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource(
 * collectionOperations={"get"},
 * itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass=HelpSectionRepository::class)
 * @UniqueEntity("title")
 */
class HelpSection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"section:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"section:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"section:read"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Help::class, inversedBy="sections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $help;

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHelp(): ?Help
    {
        return $this->help;
    }

    public function setHelp(?Help $help): self
    {
        $this->help = $help;

        return $this;
    }
}
