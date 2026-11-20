# scientific-courses

Plateforme de cours scientifiques (maths, informatique, chimie, physique) : API Symfony, front React, comptes élèves et suivi de progression.

Chaque matière est découpée en chapitres, chaque chapitre en sous-chapitres. Un sous-chapitre contient un cours concis, 2 ou 3 exemples corrigés et 20 questions corrigées, classées du facile au difficile. Chaque chapitre se termine par un examen de 20 questions. L'élève corrige lui-même chaque question (« j'avais juste » ou « j'avais faux ») et sa progression est enregistrée sur son compte.

## Structure

```
backend/    API Symfony 7.4 (PHP 8.2 ou plus)
frontend/   Application React (Vite + React Router)
data/       Contenu des cours, un fichier JSON par chapitre
ROADMAP.md  Suivi du projet : ce qui est fait, ce qui reste
```

## Prérequis

- PHP 8.2 ou plus, avec les extensions `pdo_mysql`, `intl`, `ctype`, `iconv`
- [Composer](https://getcomposer.org/)
- Node.js 20 ou plus
- MySQL 8 ou MariaDB 10.11 (WAMP, XAMPP ou Laragon conviennent)
- [Mailpit](https://mailpit.axllent.org/), qui capture les e-mails en local pour tester le mot de passe oublié

## Installation en local

### 1. L'API Symfony

```bash
cd backend
composer install
```

`composer install` peut ajouter quelques fichiers de configuration (recettes Symfony Flex). C'est normal : vérifie-les avec `git status` et valide-les dans un commit.

Crée ensuite un fichier `backend/.env.local`, jamais versionné, avec tes propres valeurs :

```dotenv
APP_SECRET=colle-ici-une-chaine-aleatoire
DATABASE_URL="mysql://root:@127.0.0.1:3306/scientific_courses?serverVersion=8.0.32&charset=utf8mb4"
```

Pour générer `APP_SECRET` : `php -r "echo bin2hex(random_bytes(16));"`.
Pour MariaDB, remplace `serverVersion=8.0.32` par ta version, par exemple `serverVersion=10.11.2-MariaDB`.

Puis crée la base, les tables, et importe le premier chapitre :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console app:import-chapter ../data/maths/01-calcul-scientifique.json
```

Lance enfin l'API :

```bash
php -S 127.0.0.1:8000 -t public
```

### 2. Mailpit

Lance Mailpit (binaire téléchargé, ou `docker run -p 8025:8025 -p 1025:1025 axllent/mailpit`).
Les e-mails envoyés par l'API apparaissent sur http://localhost:8025.

### 3. Le front React

```bash
cd frontend
npm install
npm run dev
```

Ouvre http://localhost:5173, crée un compte élève, puis ouvre le chapitre « Calcul scientifique ».

## API

Toutes les routes sont sous `/api`. Sauf mention contraire, il faut être connecté.

| Méthode | Route | Rôle |
|---|---|---|
| POST | `/register` | Créer un compte `{firstName, email, password}`. Public. |
| POST | `/login` | Se connecter `{email, password}`. Public. 5 essais ratés en 15 min, puis blocage. |
| POST | `/logout` | Se déconnecter. |
| GET | `/me` | L'élève connecté. |
| POST | `/reset-password/request` | Recevoir un lien par e-mail `{email}`. Public. Même réponse que le compte existe ou non. |
| POST | `/reset-password/reset` | Choisir un nouveau mot de passe `{token, password}`. Public. Lien valable 1 h, utilisable une fois. |
| GET | `/subjects` | Matières, chapitres et progression de l'élève. |
| GET | `/chapters/{slug}` | Un chapitre complet et les réponses déjà données. |
| PUT | `/questions/{id}/attempt` | Enregistrer `{correct: true\|false}`. |
| DELETE | `/questions/{id}/attempt` | Annuler la réponse à une question. |

## Sécurité

- Connexion par cookie de session `HttpOnly`, `SameSite=Lax`. En développement, Vite renvoie `/api` vers Symfony : front et API ont la même origine, aucune configuration CORS n'est nécessaire.
- Mots de passe hachés par l'algorithme recommandé par Symfony (`auto`), 10 caractères minimum.
- Réinitialisation par le bundle officiel `symfonycasts/reset-password-bundle` : seul un hash du jeton est stocké en base.

## Ajouter un chapitre

Crée un fichier JSON dans `data/<matière>/` sur le modèle de `data/maths/01-calcul-scientifique.json`, puis :

```bash
php bin/console app:import-chapter ../data/<matière>/<fichier>.json
```

L'option `--replace` remplace un chapitre déjà importé, mais efface les réponses des élèves sur ce chapitre.
