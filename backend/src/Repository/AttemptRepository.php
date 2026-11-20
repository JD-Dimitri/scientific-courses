<?php

namespace App\Repository;

use App\Entity\Attempt;
use App\Entity\Chapter;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attempt>
 */
class AttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attempt::class);
    }

    public function findOneFor(User $user, Question $question): ?Attempt
    {
        return $this->findOneBy(['user' => $user, 'question' => $question]);
    }

    /**
     * Réponses de l'élève pour un chapitre, sous la forme [idQuestion => juste (bool)].
     *
     * @return array<int, bool>
     */
    public function mapForChapter(User $user, Chapter $chapter): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('IDENTITY(a.question) AS questionId', 'a.correct AS correct')
            ->join('a.question', 'q')
            ->where('a.user = :user')
            ->andWhere('q.chapter = :chapter')
            ->setParameter('user', $user)
            ->setParameter('chapter', $chapter)
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['questionId']] = (bool) $row['correct'];
        }

        return $map;
    }

    /**
     * Bilan par chapitre pour un élève : [idChapitre => ['answered' => n, 'correct' => n]].
     *
     * @return array<int, array{answered: int, correct: int}>
     */
    public function summaryByChapter(User $user): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select(
                'IDENTITY(q.chapter) AS chapterId',
                'COUNT(a.id) AS answered',
                'SUM(CASE WHEN a.correct = true THEN 1 ELSE 0 END) AS correct',
            )
            ->join('a.question', 'q')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->groupBy('q.chapter')
            ->getQuery()
            ->getArrayResult();

        $summary = [];
        foreach ($rows as $row) {
            $summary[(int) $row['chapterId']] = [
                'answered' => (int) $row['answered'],
                'correct' => (int) $row['correct'],
            ];
        }

        return $summary;
    }
}
