<?php

namespace App\Entity;

use App\Repository\WorkedExampleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un exemple corrigé, affiché directement avec sa solution dans le cours.
 */
#[ORM\Entity(repositoryClass: WorkedExampleRepository::class)]
class WorkedExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'examples')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Section $section;

    #[ORM\Column(length: 150)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $statementHtml;

    #[ORM\Column(type: Types::TEXT)]
    private string $solutionHtml;

    #[ORM\Column]
    private int $position;

    public function __construct(Section $section, string $title, string $statementHtml, string $solutionHtml, int $position)
    {
        $this->section = $section;
        $this->title = $title;
        $this->statementHtml = $statementHtml;
        $this->solutionHtml = $solutionHtml;
        $this->position = $position;
        $section->addExample($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatementHtml(): string
    {
        return $this->statementHtml;
    }

    public function getSolutionHtml(): string
    {
        return $this->solutionHtml;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
