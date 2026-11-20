<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un sous-chapitre : un cours concis, des exemples corrigés et 20 questions d'entraînement.
 */
#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Chapter $chapter;

    #[ORM\Column(length: 100)]
    private string $slug;

    /** Numéro affiché, par exemple « 1.3 ». */
    #[ORM\Column(length: 10)]
    private string $number;

    #[ORM\Column(length: 150)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $courseHtml = '';

    #[ORM\Column]
    private int $position = 0;

    /** @var Collection<int, WorkedExample> */
    #[ORM\OneToMany(targetEntity: WorkedExample::class, mappedBy: 'section', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $examples;

    /** @var Collection<int, Question> */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'section')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $questions;

    public function __construct(Chapter $chapter, string $slug, string $number, string $title, int $position)
    {
        $this->chapter = $chapter;
        $this->slug = $slug;
        $this->number = $number;
        $this->title = $title;
        $this->position = $position;
        $this->examples = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $chapter->addSection($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapter(): Chapter
    {
        return $this->chapter;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCourseHtml(): string
    {
        return $this->courseHtml;
    }

    public function setCourseHtml(string $courseHtml): static
    {
        $this->courseHtml = $courseHtml;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /** @return Collection<int, WorkedExample> */
    public function getExamples(): Collection
    {
        return $this->examples;
    }

    public function addExample(WorkedExample $example): static
    {
        if (!$this->examples->contains($example)) {
            $this->examples->add($example);
        }

        return $this;
    }

    /** @return Collection<int, Question> */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
        }

        return $this;
    }
}
