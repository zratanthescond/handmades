<?php

namespace App\Entity;

use App\Repository\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource(
 * collectionOperations={"get"},
 * itemOperations={"get"},
 * normalizationContext={
 * "groups"={"section:read"}
 * }
 * )
 * @UniqueEntity("title")
 * @ORM\Entity(repositoryClass=HelpRepository::class)
 */
class Help
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
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"section:read"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=HelpSection::class, mappedBy="help", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\Valid
     * @Groups({"section:read"})
     */
    private $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|HelpSection[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(HelpSection $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setHelp($this);
        }

        return $this;
    }

    public function removeSection(HelpSection $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getHelp() === $this) {
                $section->setHelp(null);
            }
        }

        return $this;
    }
}
