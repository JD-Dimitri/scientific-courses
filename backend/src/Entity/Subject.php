<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une matière : mathématiques, informatique, chimie, physique.
 */
#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60, unique: true)]
    private string $slug;

    #[ORM\Column(length: 80)]
    private string $name;

    #[ORM\Column]
    private int $position = 0;

    /** @var Collection<int, Chapter> */
    #[ORM\OneToMany(targetEntity: Chapter::class, mappedBy: 'subject')]
    #[ORM\OrderBy(['number' => 'ASC'])]
    private Collection $chapters;

    public function __construct(string $slug, string $name, int $position = 0)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->position = $position;
        $this->chapters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /** @return Collection<int, Chapter> */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }
}
