# Project Notes

had fichier kayjma3 b ikhtisar l modifications li tدارو f projet `gs-laravel`.

## UI / Pages

- tawhid style dyal pages:
  - `articles`
  - `ventes`
  - `commandes`
- `articles` w `commandes` t9arbo f design l page `ventes`
- facture/preview tsla7 style dyalha bach tbqa qd ma ymken f page wa7da

## PDF Factures

- tzed support dyal PDF b `barryvdh/laravel-dompdf`
- routes PDF:
  - `ventes/{vente}/pdf`
  - `commandes/{commande}/pdf`
- view mخصصة l PDF:
  - `resources/views/pdf/invoice.blade.php`

## Filtres / Recherche

### Articles
- search b nom produit / reference
- filtre b `statut`
- filtre `stock bas seulement`

### Commandes
- search b reference / article / fournisseur
- filtre b `statut`
- filtre b `fournisseur`
- filtre b `date debut / date fin`

### Ventes
- search b reference / article / client
- filtre b `client`
- filtre b `mode_paiement`
- filtre b `date debut / date fin`

## Historique des actions

- creation table: `action_logs`
- page:
  - `/historique`
- kaytsjlo fih:
  - creation article
  - update article
  - changement statut article
  - restock article
  - suppression article
  - creation commande
  - update commande
  - changement statut commande
  - livraison commande
  - suppression commande
  - creation vente
  - suppression vente
  - backup database

## Backup Database

- artisan command:
  - `php artisan backup:database`
- backups kaytm7tto f:
  - `storage/app/backups`
- scheduler يومي:
  - `01:00`

## Dashboard

- tzado indicators jdod:
  - `operations today`
  - `dernier backup`

## Emails Admin

- ila tzadat `vente` jdida => system kayseft email l admin
- ila tzadat `commande` jdida => system kayseft email l admin
- admin email kaytqra men:
  - `ADMIN_EMAIL` f `.env`

## Important .env

khass ykono had variables mضبوطين ila bghiti email réel:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="GS OCP"
ADMIN_EMAIL="your-email@gmail.com"
ADMIN_NAME="Admin"
```

mn b3d ay changement f `.env`:

```bash
php artisan config:clear
```

## Files importants ajoutés

- `PROJECT_NOTES.md`
- `app/Models/ActionLog.php`
- `app/Support/ActionLogger.php`
- `app/Support/AdminActionMailer.php`
- `app/Notifications/AdminActionEmailNotification.php`
- `app/Http/Controllers/ActionLogController.php`
- `app/Console/Commands/BackupDatabase.php`
- `resources/views/historique/index.blade.php`
- `resources/views/pdf/invoice.blade.php`
- `database/migrations/2026_03_27_113500_create_action_logs_table.php`

## Commands utiles

```bash
php artisan migrate --force
php artisan backup:database
php artisan config:clear
php artisan view:cache
```
