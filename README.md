# Armor Fire CRM

PHP CRM for sales, customers, orders, channel partners, and mobile API services.

## Requirements

- PHP 5.6+ (XAMPP recommended)
- MySQL / MariaDB
- Apache

## Local Setup

1. Clone repository into XAMPP `htdocs`.
2. Import database dump separately (`crm2025.sql` is not stored in Git).
3. Copy config template:
   ```bash
   copy include\app.config.sample.php include\app.config.php
   ```
4. Edit `include/app.config.php` with local DB credentials and site URL.
5. Run DB migration if needed:
   ```sql
   ALTER TABLE `executive`
   ADD `channel_partner_flag` TINYINT NOT NULL DEFAULT 0 AFTER `turnover_year`;
   ```
6. Open: `http://localhost:8080/armor_crm_08_07/202526/bbsales_tracking/`

## Live Server Setup

1. Clone/pull repository on server.
2. Create `include/app.config.live.php` from `include/app.config.sample.php`.
3. Set live `site_url`, database user, password, and database name.
4. Import database and run migrations from `bbsales_tracking/alter.php`.
5. Ensure write permissions for upload folders:
   - `bbsales_tracking/expence_documents/`
   - `bbsales_tracking/order_documents/`
   - `bbsales_tracking/sheet_import/uploads/`
   - `resource/image/`

## GitHub Push (first time)

```bash
cd c:\xampp56\htdocs\armor_crm_08_07\202526
git init
git add .
git status
git commit -m "Initial commit: Armor CRM"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/armor-fire-crm.git
git push -u origin main
```

## Live Update

```bash
git pull origin main
```

## Channel Partner Feature

- Checkbox on all customer add/edit forms
- Menu: Sales & Marketing → Customer → Channel Partner
- Create login: customer gear menu → Create System User
- Login URL: `bbsales_tracking/index.php`
- Default password: customer mobile number (if password not set)

## Files Not in Git

- `include/app.config.php` (local secrets)
- `include/app.config.live.php` (live secrets)
- `*.sql` database dumps
- logs and user-uploaded documents
