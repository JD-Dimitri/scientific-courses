<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class AuthController extends AbstractController
{
    use JsonBodyTrait;

    public const PASSWORD_MIN_LENGTH = 10;

    /**
     * Création d'un compte élève. L'élève est connecté directement après.
     * Corps : {"firstName": "...", "email": "...", "password": "..."}
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        Security $security,
    ): JsonResponse {
        $data = $this->jsonBody($request);
        $plainPassword = (string) ($data['password'] ?? '');

        $user = (new User())
            ->setFirstName((string) ($data['firstName'] ?? ''))
            ->setEmail((string) ($data['email'] ?? ''));

        $errors = $this->violationsToArray($validator->validate($user));
        if (mb_strlen($plainPassword) < self::PASSWORD_MIN_LENGTH) {
            $errors['password'] = sprintf('Le mot de passe doit contenir au moins %d caractères.', self::PASSWORD_MIN_LENGTH);
        }
        if ($errors) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();

        $security->login($user, 'json_login', 'main');

        return $this->json(['user' => $user->toArray()], Response::HTTP_CREATED);
    }

    /**
     * Connexion. Symfony vérifie l'e-mail et le mot de passe (json_login dans security.yaml)
     * avant d'arriver ici : si on atteint cette méthode, la connexion a réussi.
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Identifiants invalides.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(['user' => $user->toArray()]);
    }

    /**
     * Déconnexion. Cette méthode n'est jamais exécutée : le pare-feu intercepte la route.
     * La réponse est fabriquée par App\EventSubscriber\LogoutSubscriber.
     */
    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): never
    {
        throw new \LogicException('Cette route est interceptée par le pare-feu.');
    }

    /** L'élève connecté, pour que le front sache qui est là au chargement. */
    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json(['user' => $user->toArray()]);
    }
}
