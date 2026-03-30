# GS-OCP - Application de gestion de stock

Application web Laravel pour le suivi du stock, des ventes, des commandes fournisseurs, des clients, des fournisseurs et de l'historique des actions.

Ce document a ete prepare pour faciliter la prise en main par un responsable, un administrateur technique ou toute personne qui doit exploiter l'application apres livraison.

## Nouveautes recentes

Les derniers ajustements visibles dans le code du projet incluent:

- nouvelle page d'accueil moderne via `welcome-modern`
- nouveau dashboard moderne via `dashboard-modern`
- acces au dashboard strictement protege par `auth` et `verified`
- remplacement des anciens flux principaux par `ArticleFlowController` et `CommandeFlowController`
- recherche des articles et des commandes enrichie avec plus de filtres metier
- creation automatique d'un `produit` si l'utilisateur saisit seulement un nom de produit lors de l'ajout d'un article
- lors de la creation d'une commande fournisseur, creation automatique d'un article associe avec quantite initiale `0` jusqu'a la livraison
- blocage metier plus strict sur les statuts de commande: une commande `livree` ou `annulee` ne peut pas etre reactivee librement
- ajout automatique de la `date_livraison` lors du passage a `livree`
- restock manuel d'article avec reactivation automatique du statut `actif` si l'article etait `epuise`
- suivi plus visible des commandes en attente dans les listes d'articles
- renforcement des notifications utilisateur et des logs d'actions sur les operations articles / commandes
- envoi d'un email admin lors de l'enregistrement d'une nouvelle commande fournisseur

## 1. Objectif du projet

L'application permet de gerer un cycle simple de stock:

- enregistrer les articles disponibles en stock
- suivre les seuils minimums et les expirations
- enregistrer les ventes aux clients
- enregistrer les commandes fournisseurs
- incrementer ou decrementer automatiquement le stock selon les operations
- consulter un tableau de bord avec indicateurs
- generer des factures PDF
- suivre les notifications et l'historique des actions
- lancer un backup de la base de donnees

## 2. Fonctionnalites metier disponibles

### Authentification et acces

- authentification Laravel Breeze
- gestion de connexion / deconnexion
- verification email active pour acceder au dashboard (`/dashboard` est protege par `auth` + `verified`)
- gestion du profil utilisateur

### Dashboard

Le tableau de bord affiche notamment:

- stock total des articles actifs
- nombre d'articles en stock bas
- nombre d'articles expires
- nombre de ventes du jour
- chiffre d'affaires du jour
- nombre de commandes en attente
- nombre d'operations du jour
- liste d'articles en stock bas
- liste d'articles expires
- ventes recentes
- dernier backup detecte dans `storage/app/backups`
- notifications non lues de l'utilisateur connecte

### Gestion des articles

- liste paginee des articles
- recherche par nom produit ou reference produit
- filtre par statut
- filtre "stock bas seulement"
- creation d'un article a partir d'un produit existant
- creation automatique d'un produit si on saisit seulement un nom de produit
- modification d'un article
- changement de statut (`actif`, `expire`, `epuise`)
- restock via action dediee
- suppression interdite si l'article est lie a des ventes ou a des commandes
- notification automatique si le stock passe sous le seuil minimum

### Gestion des ventes

- liste paginee des ventes
- recherche par reference facture, article ou client
- filtres par client, mode de paiement et plage de dates
- creation d'une vente
- generation de facture PDF
- affichage detail d'une vente
- annulation d'une vente par suppression logique
- restauration automatique du stock lors de l'annulation

### Gestion des commandes fournisseurs

- liste paginee des commandes
- recherche par reference, article ou fournisseur
- filtres par statut, fournisseur et plage de dates
- creation d'une commande fournisseur
- modification autorisee uniquement si la commande est `en_attente`
- changement de statut
- action "marquer livree"
- generation de facture PDF
- suppression interdite pour une commande deja livree
- augmentation automatique du stock quand une commande passe a `livree`

### Gestion des clients

- creation
- modification
- suppression logique
- consultation

### Gestion des fournisseurs

- creation
- modification
- suppression logique
- consultation

### Notifications

L'application utilise la table `notifications` de Laravel pour:

- notifications internes d'action utilisateur
- alertes de stock bas
- alertes d'articles expires
- marquage des notifications comme lues

### Historique des actions

Une table `action_logs` enregistre les operations importantes:

- creation, modification, suppression et restock d'articles
- changement de statut d'article
- creation, modification, suppression et livraison de commandes
- changement de statut de commande
- creation et annulation de ventes
- lancement de backup

Un ecran `/historique` permet de:

- rechercher dans les descriptions
- filtrer par type d'action
- filtrer par type de cible
- filtrer par plage de dates
- lancer un backup manuel de la base

### Backups

- commande artisan: `php artisan backup:database`
- sauvegarde des fichiers dans `storage/app/backups`
- support du backup SQLite
- support du backup MySQL via `mysqldump`
- backup manuel depuis l'ecran historique
- backup planifie tous les jours a `01:00`

### Expirations automatiques

- commande artisan: `php artisan stock:verifier-expirations`
- recherche des articles actifs dont la date d'expiration est depassee
- mise a jour du statut vers `expire`
- notification des admins via la table `notifications`
- tache planifiee tous les jours a `00:00`

### Emails

Des emails peuvent etre envoyes pour:

- nouvelle vente enregistree
- nouvelle commande enregistree
- alertes stock bas

L'adresse cible principale est lue depuis `ADMIN_EMAIL`. Si cette variable est absente, le systeme utilise l'email du premier utilisateur en base.

## 3. Regles de gestion importantes

- une vente est refusee si l'article est expire
- une vente est refusee si la quantite demandee depasse le stock disponible
- lors de la creation d'une vente, le stock est decremente automatiquement
- si le stock atteint `0`, l'article passe automatiquement au statut `epuise`
- lors de l'annulation d'une vente, le stock est restitue automatiquement
- lorsqu'une commande devient `livree`, le stock de l'article est incremente automatiquement
- un article `expire` ne peut pas etre reactive en `actif` s'il est deja expire
- un article ne peut pas etre reactive en `actif` si sa quantite est nulle
- une commande `livree` ne peut plus revenir a un autre statut
- une commande `annulee` ne peut pas etre reactivee
- une commande `livree` ne peut pas etre supprimee
- un article lie a des ventes ou commandes ne peut pas etre supprime

## 4. Technologies utilisees

### Backend

- PHP `^8.2`
- Laravel `^12`
- Eloquent ORM
- Laravel Notifications
- Laravel Scheduler
- Laravel Breeze

### Frontend

- Blade
- Vite
- Tailwind CSS
- Alpine.js

### PDF et utilitaires

- `barryvdh/laravel-dompdf` pour les factures PDF
- `concurrently` pour lancer les services de developpement

### Base de donnees

- SQLite pris en charge par defaut
- MySQL egalement pris en charge

## 5. Arborescence utile

- `app/Http/Controllers` : logique des modules
- `app/Models` : modeles Eloquent
- `app/Observers` : automatisation des mouvements de stock
- `app/Notifications` : notifications base de donnees / email
- `app/Support` : journalisation des actions et email admin
- `app/Console/Commands` : commandes backup et verification des expirations
- `database/migrations` : structure de la base
- `resources/views` : interfaces Blade
- `resources/views/pdf/invoice.blade.php` : template PDF
- `routes/web.php` : routes web principales
- `routes/console.php` : planification des commandes
- `storage/app/backups` : sauvegardes generees
- `storage/logs` : logs applicatifs et logs des taches planifiees
- `reception_commande_sequence.drawio` : diagramme de sequence commande
- `sortie_mouvement_sequence.drawio` : diagramme de sequence sortie stock / vente

## 6. Modules et routes principales

Routes visibles dans `routes/web.php`:

- `/` : page d'accueil
- `/dashboard` : tableau de bord
- `/profile` : profil utilisateur
- `/clients` : CRUD clients
- `/fournisseurs` : CRUD fournisseurs
- `/articles` : CRUD articles
- `/articles/{article}/statut` : changement de statut article
- `/articles/{article}/restock` : ajout de stock
- `/ventes` : CRUD ventes (sans edition)
- `/ventes/{vente}/pdf` : export PDF vente
- `/commandes` : CRUD commandes
- `/commandes/{commande}/pdf` : export PDF commande
- `/commandes/{commande}/livrer` : marquer une commande comme livree
- `/commandes/{commande}/statut` : changement de statut commande
- `/historique` : historique des actions
- `/historique/backup` : lancement d'un backup manuel
- `/notifications/mark-as-read` : marquer les notifications comme lues

## 7. Structure de la base de donnees

Tables metier principales:

- `users`
- `clients`
- `fournisseurs`
- `produits`
- `articles`
- `ventes`
- `commandes`
- `notifications`
- `action_logs`
- `sessions`

Relations principales:

- un `produit` possede plusieurs `articles`
- un `article` appartient a un `produit`
- un `article` possede plusieurs `ventes`
- un `article` possede plusieurs `commandes`
- un `client` possede plusieurs `ventes`
- un `fournisseur` possede plusieurs `commandes`

## 8. Installation du projet

### Prerequis

- PHP 8.2 ou plus
- Composer
- Node.js + npm
- SQLite ou MySQL
- `mysqldump` si vous utilisez MySQL et souhaitez generer des backups SQL

### Installation rapide

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate
npm run build
```

Le projet contient aussi un script Composer pratique:

```bash
composer run setup
```

Ce script execute:

- installation PHP
- creation du fichier `.env` si absent
- generation de la cle applicative
- migration de la base
- installation des dependances front
- build front

## 9. Configuration `.env`

### Variables minimales

Verifier au minimum:

```env
APP_NAME="GS OCP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Exemple SQLite

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Si le fichier SQLite n'existe pas:

```bash
New-Item -ItemType File database/database.sqlite
```

### Exemple MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gs_laravel
DB_USERNAME=root
DB_PASSWORD=
DB_DUMP_BINARY=C:\xampp\mysql\bin\mysqldump.exe
```

`DB_DUMP_BINARY` est utile pour le backup MySQL si `mysqldump` n'est pas disponible dans le `PATH`.

### Configuration email recommandee

Pour activer les vrais envois d'emails:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="GS OCP"
ADMIN_EMAIL="admin@example.com"
ADMIN_NAME="Responsable Stock"
```

Par defaut, `.env.example` utilise `MAIL_MAILER=log`, ce qui signifie que les emails sont ecrits dans les logs et ne sont pas envoyes reellement.

Apres toute modification de `.env`:

```bash
php artisan config:clear
```

## 10. Lancement en local

### Mode developpement complet

```bash
composer run dev
```

Cette commande lance:

- le serveur Laravel
- l'ecoute de la queue
- l'affichage de logs via `pail`
- Vite en mode dev

### Lancement manuel

```bash
php artisan serve
npm run dev
php artisan queue:listen --tries=1 --timeout=0
```

### Build production des assets

```bash
npm run build
```

## 10.bis Premiere mise en route

Si vous lancez l'application pour la toute premiere fois sur une nouvelle machine, suivez cet ordre:

### Etape 1. Installer les dependances

```bash
composer install
npm install
```

### Etape 2. Preparer le fichier d'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Puis verifier dans `.env`:

- la connexion base de donnees
- `APP_URL`
- les parametres mail si vous voulez de vrais emails
- `ADMIN_EMAIL` si le responsable doit recevoir les alertes

### Etape 3. Preparer la base de donnees

#### Cas SQLite

Creer le fichier s'il n'existe pas:

```bash
New-Item -ItemType File database/database.sqlite
```

Puis definir dans `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

#### Cas MySQL

Creer d'abord une base vide, par exemple `gestion-stock-ocp`, puis definir dans `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion-stock-ocp
DB_USERNAME=root
DB_PASSWORD=
```

### Etape 4. Lancer les migrations

```bash
php artisan migrate
```

Si vous voulez aussi inserer l'utilisateur de test:

```bash
php artisan db:seed
```

### Etape 5. Compiler les assets front

```bash
npm run build
```

Ou en mode developpement:

```bash
npm run dev
```

### Etape 6. Lancer l'application

Option simple:

```bash
composer run dev
```

Ou manuellement:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
npm run dev
```

### Etape 7. Ouvrir l'application

Aller sur:

- `http://127.0.0.1:8000`

### Etape 8. Se connecter

Si vous avez lance le seeder:

- email: `test@example.com`
- mot de passe: `password`

Sinon, creer un compte depuis l'ecran d'inscription.

### Etape 9. Verifier l'email si necessaire

Le dashboard est protege par la verification email.

Selon votre configuration:

- soit vous utilisez l'email de verification reel
- soit vous marquez manuellement l'utilisateur comme verifie en base de donnees

### Etape 10. Commencer les premiers tests metier

Ordre conseille pour tester l'application:

1. creer un fournisseur
2. creer un client
3. creer un article
4. creer une commande fournisseur
5. marquer la commande comme `livree`
6. creer une vente
7. verifier le dashboard
8. verifier l'historique
9. generer un PDF de vente ou de commande

### Etape 11. Verifier les automatismes

Pour confirmer que tout fonctionne:

- verifier que le stock baisse apres une vente
- verifier que le stock augmente apres une commande livree
- verifier qu'une notification apparait en cas de stock bas
- verifier que l'historique enregistre les actions
- verifier qu'un backup manuel fonctionne depuis `/historique`

## 11. Base de donnees et donnees initiales

### Migrations

```bash
php artisan migrate
```

### Reinitialisation complete

```bash
php artisan migrate:fresh --seed
```

### Seeder disponible

Le seeder actuel cree un utilisateur de test:

- email: `test@example.com`
- mot de passe: `password`

Attention:

- cet utilisateur n'est cree que si vous lancez le seeding
- il faut verifier l'etat `email_verified_at` selon votre besoin d'acces au dashboard

## 12. Commandes artisan utiles

```bash
php artisan migrate
php artisan db:seed
php artisan backup:database
php artisan stock:verifier-expirations
php artisan config:clear
php artisan view:cache
php artisan optimize
php artisan schedule:run
php artisan test
```

## 13. Taches planifiees

Les taches configurees dans `routes/console.php` sont:

- `stock:verifier-expirations` tous les jours a `00:00`
- `backup:database` tous les jours a `01:00`
- `queue:prune-failed` chaque semaine

Pour que ces taches fonctionnent en serveur, il faut configurer le scheduler Laravel:

```bash
php artisan schedule:run
```

En production, cette commande doit etre appelee regulierement par le planificateur du serveur.

## 14. Fonctionnement metier resume

### Flux vente

1. l'utilisateur choisit un article actif avec stock disponible
2. le systeme verifie que l'article n'est pas expire
3. le systeme verifie la quantite disponible
4. la vente est creee avec prix unitaire et total
5. le stock est diminue automatiquement
6. si le stock atteint le seuil bas, une notification est envoyee
7. si le stock devient nul, le statut de l'article passe a `epuise`

### Flux commande fournisseur

1. l'utilisateur cree une commande pour un article et un fournisseur
2. la commande reste `en_attente` jusqu'a reception
3. quand elle est marquee `livree`, le stock augmente automatiquement
4. si l'article etait `epuise`, il redevient `actif`

### Flux expiration

1. la commande planifiee cherche les articles actifs expires
2. le statut de ces articles passe a `expire`
3. une notification est envoyee aux admins

## 15. Points d'attention pour le responsable

- l'application depend fortement des notifications base de donnees; il faut garder la table `notifications`
- le backup MySQL necessite un binaire `mysqldump` accessible
- le dashboard n'est accessible qu'aux utilisateurs authentifies et verifies
- la verification des expirations cherche des utilisateurs avec `role = admin`, mais la table `users` livree actuellement ne contient pas ce champ dans sa migration par defaut
- les emails admin ne partiront pas si la configuration mail n'est pas complete
- les produits ne sont pas exposes par une route CRUD dediee dans `routes/web.php`; ils sont surtout manipules a travers les articles
- les tests presents couvrent surtout la base Laravel/Breeze et non tout le metier stock/vente/commande

## 16. Tests et qualite

Tests disponibles:

- tests d'authentification Breeze
- tests profil
- tests d'exemple Laravel

Lancer les tests:

```bash
php artisan test
```

## 17. Preparation pour mise en production

Checklist recommandee:

1. configurer `APP_ENV=production`
2. configurer `APP_DEBUG=false`
3. configurer une vraie base de donnees
4. configurer le service mail
5. definir `ADMIN_EMAIL`
6. verifier `DB_DUMP_BINARY` si MySQL
7. executer `php artisan migrate --force`
8. executer `npm run build`
9. vider et reconstruire les caches Laravel
10. configurer le scheduler Laravel sur le serveur
11. verifier les permissions d'ecriture sur `storage` et `bootstrap/cache`

Commandes utiles:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 18. Depannage rapide

### Les emails ne partent pas

- verifier `MAIL_*`
- verifier `ADMIN_EMAIL`
- lancer `php artisan config:clear`
- verifier `storage/logs/laravel.log`

### Le backup echoue

- verifier la configuration base de donnees
- verifier que `storage/app/backups` est accessible en ecriture
- pour MySQL, verifier `DB_DUMP_BINARY` ou la disponibilite de `mysqldump`

### Les taches planifiees ne se lancent pas

- verifier que le scheduler serveur appelle bien `php artisan schedule:run`
- verifier les logs:
  - `storage/logs/expirations.log`
  - `storage/logs/database-backup.log`

### Les notifications ne s'affichent pas

- verifier que la migration `notifications` a bien ete executee
- verifier que l'utilisateur est connecte

## 19. Resume executif

Ce projet est une application Laravel de gestion de stock orientee exploitation quotidienne:

- suivi du stock et des seuils minimums
- ventes avec decrement automatique du stock
- commandes fournisseurs avec increment automatique a la livraison
- alertes, historique et dashboard
- export PDF
- backup manuel et automatique

Pour une utilisation responsable et stable, il faut surtout bien configurer:

- la base de donnees
- les emails
- le scheduler
- les backups
- les comptes utilisateurs
