<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Lit le corps JSON d'une requête et renvoie un tableau.
 */
trait JsonBodyTrait
{
    /** @return array<string, mixed> */
    private function jsonBody(Request $request): array
    {
        try {
            $data = json_decode($request->getContent() ?: '{}', true, 16, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('Le corps de la requête doit être du JSON valide.');
        }

        if (!\is_array($data)) {
            throw new BadRequestHttpException('Le corps de la requête doit être un objet JSON.');
        }

        return $data;
    }

    /**
     * Transforme une liste de violations du validateur en tableau [champ => message].
     *
     * @return array<string, string>
     */
    private function violationsToArray(iterable $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] ??= $violation->getMessage();
        }

        return $errors;
    }
}
