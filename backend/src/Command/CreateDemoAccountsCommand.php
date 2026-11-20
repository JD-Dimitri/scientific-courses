<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Crée (ou remet à zéro) les deux comptes de test :
 *   - admin  / admin-test  (administrateur)
 *   - eleve1 / eleve-test  (élève)
 *
 *   php bin/console app:create-demo-accounts
 *
 * Refusé en production : ces mots de passe sont publics, ils sont écrits dans le dépôt.
 */
#[AsCommand(name: 'app:create-demo-accounts', description: 'Crée les comptes de test admin et eleve1 (développement uniquement)')]
class CreateDemoAccountsCommand extends Command
{
    private const ACCOUNTS = [
        ['username' => 'admin', 'email' => 'admin@scientific-courses.local', 'firstName' => 'Admin', 'password' => 'admin-test', 'roles' => [User::ROLE_ADMIN]],
        ['username' => 'eleve1', 'email' => 'eleve1@scientific-courses.local', 'firstName' => 'Élève 1', 'password' => 'eleve-test', 'roles' => []],
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $hasher,
        #[Autowire('%kernel.environment%')] private readonly string $environment,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ('prod' === $this->environment) {
            $io->error('Commande refusée en production : les mots de passe de test sont publics.');

            return Command::FAILURE;
        }

        $rows = [];
        foreach (self::ACCOUNTS as $account) {
            $user = $this->users->loadUserByIdentifier($account['username']);
            $created = null === $user;
            $user ??= new User();

            $user->setUsername($account['username'])
                ->setEmail($account['email'])
                ->setFirstName($account['firstName'])
                ->setRoles($account['roles']);
            $user->setPassword($this->hasher->hashPassword($user, $account['password']));
            $this->em->persist($user);

            $rows[] = [$account['username'], $account['password'], $account['email'], [] === $account['roles'] ? 'élève' : 'administrateur', $created ? 'créé' : 'mot de passe remis à zéro'];
        }
        $this->em->flush();

        $io->table(['Identifiant', 'Mot de passe', 'E-mail', 'Rôle', 'État'], $rows);
        $io->success('Comptes de test prêts.');

        return Command::SUCCESS;
    }
}
