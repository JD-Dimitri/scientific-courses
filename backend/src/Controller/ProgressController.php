<?php

namespace App\Controller;

use App\Entity\Attempt;
use App\Entity\User;
use App\Repository\AttemptRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Enregistre l'auto-évaluation de l'élève sur une question.
 */
#[Route('/api/questions/{id}/attempt', requirements: ['id' => Requirement::DIGITS])]
class ProgressController extends AbstractController
{
    use JsonBodyTrait;

    /** Corps : {"correct": true} ou {"correct": false} */
    #[Route('', name: 'api_attempt_save', methods: ['PUT'])]
    public function save(
        int $id,
        Request $request,
        QuestionRepository $questions,
        AttemptRepository $attempts,
        EntityManagerInterface $em,
    ): JsonResponse {
        $question = $questions->find($id);
        if (null === $question) {
            return $this->json(['error' => 'Question introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $correct = $this->jsonBody($request)['correct'] ?? null;
        if (!\is_bool($correct)) {
            return $this->json(['error' => 'Le champ « correct » doit valoir true ou false.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User $user */
        $user = $this->getUser();
        $attempt = $attempts->findOneFor($user, $question);
        if (null === $attempt) {
            $em->persist(new Attempt($user, $question, $correct));
        } else {
            $attempt->setCorrect($correct);
        }
        $em->flush();

        return $this->json(['questionId' => $id, 'correct' => $correct]);
    }

    /** Annule l'auto-évaluation, par exemple pour refaire la question. */
    #[Route('', name: 'api_attempt_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        QuestionRepository $questions,
        AttemptRepository $attempts,
        EntityManagerInterface $em,
    ): Response {
        $question = $questions->find($id);
        if (null === $question) {
            return $this->json(['error' => 'Question introuvable.'], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $this->getUser();
        $attempt = $attempts->findOneFor($user, $question);
        if (null !== $attempt) {
            $em->remove($attempt);
            $em->flush();
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
