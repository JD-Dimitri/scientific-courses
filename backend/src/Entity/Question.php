<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une question corrigée.
 * - Entraînement : rattachée à un sous-chapitre (20 par sous-chapitre).
 * - Examen : rattachée au chapitre seul, sans sous-chapitre (20 par chapitre).
 */
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    public const KIND_PRACTICE = 'practice';
    public const KIND_EXAM = 'exam';

    public const LEVEL_EASY = 1;
    public const LEVEL_MEDIUM = 2;
    public const LEVEL_HARD = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Chapter $chapter;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Section $section;

    #[ORM\Column(length: 10)]
    private string $kind;

    /** 1 facile, 2 moyen, 3 difficile */
    #[ORM\Column(type: Types::SMALLINT)]
    private int $level;

    #[ORM\Column(type: Types::TEXT)]
    private string $statementHtml;

    #[ORM\Column(type: Types::TEXT)]
    private string $correctionHtml;

    #[ORM\Column]
    private int $position;

    public function __construct(
        Chapter $chapter,
        ?Section $section,
        string $kind,
        int $level,
        string $statementHtml,
        string $correctionHtml,
        int $position,
    ) {
        if (!\in_array($kind, [self::KIND_PRACTICE, self::KIND_EXAM], true)) {
            throw new \InvalidArgumentException(sprintf('Type de question inconnu : %s', $kind));
        }
        if ($level < self::LEVEL_EASY || $level > self::LEVEL_HARD) {
            throw new \InvalidArgumentException(sprintf('Niveau de question invalide : %d', $level));
        }
        $this->chapter = $chapter;
        $this->section = $section;
        $this->kind = $kind;
        $this->level = $level;
        $this->statementHtml = $statementHtml;
        $this->correctionHtml = $correctionHtml;
        $this->position = $position;
        $chapter->addQuestion($this);
        $section?->addQuestion($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapter(): Chapter
    {
        return $this->chapter;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getStatementHtml(): string
    {
        return $this->statementHtml;
    }

    public function getCorrectionHtml(): string
    {
        return $this->correctionHtml;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'level' => $this->level,
            'statementHtml' => $this->statementHtml,
            'correctionHtml' => $this->correctionHtml,
        ];
    }
}
