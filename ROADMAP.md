# Suivi du projet

## Méthode commune à toutes les matières

Pour chaque chapitre :

- des sous-chapitres, chacun avec un cours concis et synthétique, 2 ou 3 exemples corrigés et 20 questions corrigées ;
- un examen de 20 questions par chapitre ;
- des questions classées du facile au difficile (niveaux 1, 2, 3).

Niveau visé pour la première version : de la terminale à l'entrée en prépa.

## Fait

- [x] Structure du dépôt : `backend/` Symfony 7.4, `frontend/` React, `data/`
- [x] Compte élève : inscription, connexion, déconnexion, session par cookie, limite de 5 essais en 15 minutes
- [x] Connexion par e-mail ou par identifiant
- [x] Rôle administrateur et page Administration (liste des élèves et de leur progression)
- [x] Comptes de test `admin` / `admin-test` et `eleve1` / `eleve-test` (commande `app:create-demo-accounts`, refusée en production)
- [x] Bouton œil pour afficher ou masquer le mot de passe ; lien « Mot de passe oublié ? » sous le champ de connexion
- [x] Administration : envoi d'un lien de réinitialisation à un élève (limite : un lien toutes les 15 minutes par compte)
- [x] Version autonome de démonstration (`scientific-courses-demo.html`, hors dépôt) : fausse API dans le navigateur, boîte mail simulée
- [x] Mot de passe oublié : e-mail avec lien valable 1 h, utilisable une fois (Mailpit en local)
- [x] Modèle de données : matière, chapitre, sous-chapitre, exemple corrigé, question (entraînement ou examen), réponse de l'élève
- [x] API des cours et de la progression
- [x] Commande `app:import-chapter`
- [x] Front : connexion, inscription, mot de passe oublié, nouveau mot de passe, tableau de bord, page chapitre
- [x] Maths, chapitre 1 « Calcul scientifique » converti en JSON (5 sous-chapitres, 100 questions, examen de 20)
- [x] Maths, chapitre 2 « Algèbre et équations » (5 sous-chapitres, 100 questions, examen de 20), source dans `data/maths/src/build_02.py`

## Non vérifié

Le code a été écrit dans un environnement sans accès aux paquets Composer et npm. Seule la syntaxe a été contrôlée (`php -l`, esbuild). Rien n'a encore été exécuté : à tester en local en suivant le README.

## À faire

- [ ] Premier lancement en local et corrections
- [x] Validation du format du chapitre pilote par l'auteur
- [ ] Figure interactive « échelle des puissances de 10 » du sous-chapitre 1.1, à refaire en composant React
- [ ] Maths : chapitres 3 à 14
- [ ] Informatique, chimie, physique : plans puis chapitres
- [ ] Tests automatisés (PHPUnit pour l'API)
- [ ] Choix de l'hébergement et d'un vrai service d'envoi d'e-mails pour la production

## Programme de maths

Les chapitres marqués UTC704 couvrent l'UE « Mathématiques » du Cnam (calcul scientifique, fonctions, fonctions usuelles, dérivées et intégrales, équations différentielles).

1. Calcul scientifique (UTC704)
2. Algèbre et équations
3. Fonctions, généralités (UTC704)
4. Fonctions usuelles et modélisation (UTC704)
5. Limites et continuité
6. Dérivation (UTC704)
7. Intégration (UTC704)
8. Équations différentielles (UTC704)
9. Suites et récurrence
10. Nombres complexes
11. Géométrie : vecteurs et espace
12. Matrices et 3D
13. Dénombrement et probabilités
14. Arithmétique
