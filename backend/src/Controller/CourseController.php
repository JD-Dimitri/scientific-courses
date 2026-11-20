<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Question;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\WorkedExample;
use App\Repository\AttemptRepository;
use App\Repository\ChapterRepository;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class CourseController extends AbstractController
{
    /**
     * Les matières et leurs chapitres, avec la progression de l'élève sur chaque chapitre.
     */
    #[Route('/subjects', name: 'api_subjects', methods: ['GET'])]
    public function subjects(SubjectRepository $subjects, AttemptRepository $attempts): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $summary = $attempts->summaryByChapter($user);

        $data = array_map(fn (Subject $subject) => [
            'slug' => $subject->getSlug(),
            'name' => $subject->getName(),
            'chapters' => array_map(fn (Chapter $chapter) => [
                'slug' => $chapter->getSlug(),
                'number' => $chapter->getNumber(),
                'title' => $chapter->getTitle(),
                'cnamCode' => $chapter->getCnamCode(),
                'questionCount' => $chapter->getQuestions()->count(),
                'answered' => $summary[$chapter->getId()]['answered'] ?? 0,
                'correct' => $summary[$chapter->getId()]['correct'] ?? 0,
            ], $subject->getChapters()->toArray()),
        ], $subjects->findBy([], ['position' => 'ASC']));

        return $this->json(['subjects' => $data]);
    }

    /**
     * Un chapitre complet : sous-chapitres (cours, exemples, questions), examen,
     * et les réponses déjà données par l'élève.
     */
    #[Route('/chapters/{slug}', name: 'api_chapter', methods: ['GET'])]
    public function chapter(string $slug, ChapterRepository $chapters, AttemptRepository $attempts): JsonResponse
    {
        $chapter = $chapters->findOneBy(['slug' => $slug]);
        if (null === $chapter) {
            return $this->json(['error' => 'Chapitre introuvable.'], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'chapter' => [
                'slug' => $chapter->getSlug(),
                'number' => $chapter->getNumber(),
                'title' => $chapter->getTitle(),
                'cnamCode' => $chapter->getCnamCode(),
                'introHtml' => $chapter->getIntroHtml(),
                'subject' => [
                    'slug' => $chapter->getSubject()->getSlug(),
                    'name' => $chapter->getSubject()->getName(),
                ],
                'sections' => array_map(fn (Section $section) => [
                    'slug' => $section->getSlug(),
                    'number' => $section->getNumber(),
                    'title' => $section->getTitle(),
                    'courseHtml' => $section->getCourseHtml(),
                    'examples' => array_map(fn (WorkedExample $example) => [
                        'title' => $example->getTitle(),
                        'statementHtml' => $example->getStatementHtml(),
                        'solutionHtml' => $example->getSolutionHtml(),
                    ], $section->getExamples()->toArray()),
                    'questions' => array_map(
                        fn (Question $q) => $q->toArray(),
                        array_values($section->getQuestions()->toArray()),
                    ),
                ], array_values($chapter->getSections()->toArray())),
                'exam' => array_map(fn (Question $q) => $q->toArray(), $chapter->getExamQuestions()),
            ],
            'attempts' => (object) $attempts->mapForChapter($user, $chapter),
        ]);
    }
}
