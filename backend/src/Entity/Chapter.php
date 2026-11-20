<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un chapitre d'une matière. Il contient des sous-chapitres et un examen final.
 */
#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false)]
    private Subject $subject;

    #[ORM\Column(length: 100, unique: true)]
    private string $slug;

    #[ORM\Column]
    private int $number;

    #[ORM\Column(length: 150)]
    private string $title;

    /** Code d'une UE du Cnam couverte par ce chapitre, par exemple UTC704. */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cnamCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introHtml = null;

    /** @var Collection<int, Section> */
    #[ORM\OneToMany(targetEntity: Section::class, mappedBy: 'chapter', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $sections;

    /** @var Collection<int, Question> */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'chapter', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $questions;

    public function __construct(Subject $subject, string $slug, int $number, string $title)
    {
        $this->subject = $subject;
        $this->slug = $slug;
        $this->number = $number;
        $this->title = $title;
        $this->sections = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCnamCode(): ?string
    {
        return $this->cnamCode;
    }

    public function setCnamCode(?string $cnamCode): static
    {
        $this->cnamCode = $cnamCode;

        return $this;
    }

    public function getIntroHtml(): ?string
    {
        return $this->introHtml;
    }

    public function setIntroHtml(?string $introHtml): static
    {
        $this->introHtml = $introHtml;

        return $this;
    }

    /** @return Collection<int, Section> */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
        }

        return $this;
    }

    /**
     * Toutes les questions du chapitre : entraînement et examen.
     *
     * @return Collection<int, Question>
     */
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

    /** @return list<Question> */
    public function getExamQuestions(): array
    {
        return array_values($this->questions->filter(
            fn (Question $q) => Question::KIND_EXAM === $q->getKind()
        )->toArray());
    }
}
