<?php

namespace App\Command;

use App\Entity\Chapter;
use App\Entity\Question;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\WorkedExample;
use App\Repository\ChapterRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Importe un chapitre depuis un fichier JSON du dossier data/.
 *
 *   php bin/console app:import-chapter ../data/maths/01-calcul-scientifique.json
 *
 * Avec --replace, un chapitre déjà présent est supprimé puis réimporté.
 * Attention : les réponses des élèves sur ce chapitre sont alors perdues.
 */
#[AsCommand(name: 'app:import-chapter', description: 'Importe un chapitre (cours, exemples, questions, examen) depuis un fichier JSON')]
class ImportChapterCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SubjectRepository $subjects,
        private readonly ChapterRepository $chapters,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin du fichier JSON')
            ->addOption('replace', null, InputOption::VALUE_NONE, 'Remplace le chapitre s\'il existe déjà (efface les réponses des élèves)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        if (!is_file($file)) {
            $io->error(sprintf('Fichier introuvable : %s', $file));

            return Command::FAILURE;
        }

        try {
            $data = json_decode((string) file_get_contents($file), true, 64, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $io->error(sprintf('JSON invalide : %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $errors = $this->validate($data);
        if ($errors) {
            $io->error(array_merge(['Le fichier ne respecte pas le format attendu :'], $errors));

            return Command::FAILURE;
        }

        $existing = $this->chapters->findOneBy(['slug' => $data['chapter']['slug']]);
        if (null !== $existing) {
            if (!$input->getOption('replace')) {
                $io->error(sprintf('Le chapitre « %s » existe déjà. Relance avec --replace pour le remplacer.', $data['chapter']['slug']));

                return Command::FAILURE;
            }
            $this->em->remove($existing);
            $this->em->flush();
        }

        $s = $data['subject'];
        $subject = $this->subjects->findOneBy(['slug' => $s['slug']]);
        if (null === $subject) {
            $subject = new Subject($s['slug'], $s['name'], (int) ($s['position'] ?? 0));
            $this->em->persist($subject);
        } else {
            $subject->setName($s['name'])->setPosition((int) ($s['position'] ?? $subject->getPosition()));
        }

        $c = $data['chapter'];
        $chapter = (new Chapter($subject, $c['slug'], (int) $c['number'], $c['title']))
            ->setCnamCode($c['cnamCode'] ?? null)
            ->setIntroHtml($c['introHtml'] ?? null);
        $this->em->persist($chapter);

        $questionCount = 0;
        foreach ($data['sections'] as $i => $sec) {
            $section = (new Section($chapter, $sec['slug'], $sec['number'], $sec['title'], $i + 1))
                ->setCourseHtml($sec['courseHtml']);
            foreach ($sec['examples'] ?? [] as $j => $ex) {
                new WorkedExample($section, $ex['title'], $ex['statementHtml'], $ex['solutionHtml'], $j + 1);
            }
            foreach ($sec['questions'] as $j => $q) {
                new Question($chapter, $section, Question::KIND_PRACTICE, (int) $q['level'], $q['statementHtml'], $q['correctionHtml'], $j + 1);
                ++$questionCount;
            }
        }
        foreach ($data['exam'] as $j => $q) {
            new Question($chapter, null, Question::KIND_EXAM, (int) $q['level'], $q['statementHtml'], $q['correctionHtml'], $j + 1);
            ++$questionCount;
        }

        $this->em->flush();

        $io->success(sprintf(
            'Chapitre %d « %s » importé : %d sous-chapitres, %d questions (dont %d d\'examen).',
            $chapter->getNumber(),
            $chapter->getTitle(),
            \count($data['sections']),
            $questionCount,
            \count($data['exam']),
        ));

        return Command::SUCCESS;
    }

    /** @return list<string> */
    private function validate(mixed $data): array
    {
        $errors = [];
        if (!\is_array($data)) {
            return ['La racine doit être un objet JSON.'];
        }
        foreach (['subject' => ['slug', 'name'], 'chapter' => ['slug', 'number', 'title']] as $key => $fields) {
            foreach ($fields as $field) {
                if (!isset($data[$key][$field])) {
                    $errors[] = sprintf('Champ manquant : %s.%s', $key, $field);
                }
            }
        }
        if (!isset($data['sections']) || !\is_array($data['sections']) || [] === $data['sections']) {
            $errors[] = 'Il faut au moins un sous-chapitre dans « sections ».';
        } else {
            foreach ($data['sections'] as $i => $sec) {
                foreach (['slug', 'number', 'title', 'courseHtml', 'questions'] as $field) {
                    if (!isset($sec[$field])) {
                        $errors[] = sprintf('Champ manquant : sections[%d].%s', $i, $field);
                    }
                }
                $errors = array_merge($errors, $this->validateQuestions($sec['questions'] ?? [], sprintf('sections[%d].questions', $i)));
            }
        }
        if (!isset($data['exam']) || !\is_array($data['exam'])) {
            $errors[] = 'Le champ « exam » doit être une liste de questions.';
        } else {
            $errors = array_merge($errors, $this->validateQuestions($data['exam'], 'exam'));
        }

        return $errors;
    }

    /** @return list<string> */
    private function validateQuestions(mixed $questions, string $path): array
    {
        if (!\is_array($questions)) {
            return [sprintf('%s doit être une liste.', $path)];
        }
        $errors = [];
        foreach ($questions as $i => $q) {
            if (!\in_array($q['level'] ?? null, [1, 2, 3], true)) {
                $errors[] = sprintf('%s[%d].level doit valoir 1, 2 ou 3.', $path, $i);
            }
            if (empty($q['statementHtml']) || empty($q['correctionHtml'])) {
                $errors[] = sprintf('%s[%d] : énoncé ou correction manquant.', $path, $i);
            }
        }

        return $errors;
    }
}
