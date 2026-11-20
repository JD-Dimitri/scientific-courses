<?php

namespace App\Entity;

use App\Repository\AttemptRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * L'auto-évaluation d'un élève sur une question : « j'avais juste » ou « j'avais faux ».
 * Une seule ligne par élève et par question : on la met à jour à chaque nouvelle réponse.
 */
#[ORM\Entity(repositoryClass: AttemptRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_attempt_user_question', columns: ['user_id', 'question_id'])]
class Attempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Question $question;

    #[ORM\Column]
    private bool $correct;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $answeredAt;

    public function __construct(User $user, Question $question, bool $correct)
    {
        $this->user = $user;
        $this->question = $question;
        $this->setCorrect($correct);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): static
    {
        $this->correct = $correct;
        $this->answeredAt = new \DateTimeImmutable();

        return $this;
    }

    public function getAnsweredAt(): \DateTimeImmutable
    {
        return $this->answeredAt;
    }
}
