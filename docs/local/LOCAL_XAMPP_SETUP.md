# Gut Reset — Local Visual Studio Code + XAMPP Setup

This package is intended for local UI/UX and functional finalisation before Phase 3 external integrations.

## 1. Important architecture rule

Phase 1C contains **66 business tables**. The authoritative SQL is:

`database/schema/phase1c_final_production_mysql.sql`

The Laravel migrations under `database/migrations/phase1c/` reproduce the Phase 1C schema 1:1 and are provided for migration-based deployments.

Do **not** import the SQL and then run the Phase 1C migrations against the same empty database. Choose one database bootstrap method.

For the XAMPP visual-testing setup, use the **SQL import method**.

`database/schema/phase2_infrastructure.sql` is optional framework infrastructure for Laravel Sanctum API bearer tokens. It adds `personal_access_tokens`; it is not part of the 66-table Phase 1C business schema.

## 2. Requirements

- Windows 10/11
- Visual Studio Code
- XAMPP with Apache and a **MySQL 8-compatible MySQL server**
- PHP 8.3+
- Composer 2.x
- Node.js 20+ recommended
- npm

The project targets PHP 8.3+ and Laravel 13.

### XAMPP warning

Check the database engine bundled with your XAMPP distribution. The Phase 1C schema is designed for **MySQL 8 / InnoDB / utf8mb4**. If your XAMPP package provides MariaDB instead of MySQL 8, use a separate MySQL 8 server or a XAMPP configuration with MySQL 8 rather than silently changing the schema.

## 3. Copy project

Extract the ZIP to:

`C:\xampp\htdocs\gutreset`

Open that folder in VS Code.

## 4. Start XAMPP

Start:

- Apache
- MySQL

Do not start Redis for the local visual-testing setup. The local `.env` uses `QUEUE_CONNECTION=sync` so Phase 2C/2D can be exercised without procuring Redis.

## 5. Install PHP dependencies

Open a VS Code terminal:

```powershell
cd C:\xampp\htdocs\gutreset
composer install
```

If Composer is not on PATH, use the Composer executable installed on your Windows machine.

## 6. Create `.env`

```powershell
copy .env.xampp.example .env
php artisan key:generate
```

The local database name is:

`gut_reset`

The default XAMPP MySQL username is:

`root`

The default password is blank unless you changed it.

If you have a MySQL root password, edit:

```env
DB_PASSWORD=YOUR_PASSWORD
```

## 7. Import Phase 1C database

From the VS Code terminal:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -p < database\schema\phase1c_final_production_mysql.sql
```

If your XAMPP root user has no password:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root < database\schema\phase1c_final_production_mysql.sql
```

The SQL creates:

`gut_reset`

and all **66 Phase 1C business tables**.

## 8. Seed local demo/reference data

After importing the SQL:

```powershell
php artisan db:seed
```

The development seeder creates:

### Admin

Email:

`admin@gutreset.local`

Password:

`ChangeMe!123`

### Customer

Email:

`customer@gutreset.local`

Password:

`ChangeMe!123`

**Change these passwords immediately if the environment is ever exposed beyond your local machine.**

## 9. Optional: enable API bearer-token authentication

The Phase 1C business schema deliberately does not contain Laravel's framework token table.

For Phase 2 API testing, run:

```powershell
php artisan migrate --path=database/migrations/phase2_infrastructure
```

This adds only:

`personal_access_tokens`

It does not change any Phase 1C business table.

Alternatively, import:

`database/schema/phase2_infrastructure.sql`

## 10. Install frontend dependencies

```powershell
npm install
npm run build
```

During active UI development use:

```powershell
npm run dev
```

Keep the Vite terminal open while editing CSS/JS.

## 11. Storage

Run once:

```powershell
php artisan storage:link
```

Uploaded/public media will then be available through `/storage`.

## 12. Apache VirtualHost

Edit:

`C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Add:

```apache
<VirtualHost *:80>
    ServerName gutreset.local
    DocumentRoot "C:/xampp/htdocs/gutreset/public"

    <Directory "C:/xampp/htdocs/gutreset/public">
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>

    ErrorLog "logs/gutreset-error.log"
    CustomLog "logs/gutreset-access.log" common
</VirtualHost>
```

Make sure Apache `mod_rewrite` is enabled.

## 13. Windows hosts file

Open Notepad as Administrator and edit:

`C:\Windows\System32\drivers\etc\hosts`

Add:

```text
127.0.0.1 gutreset.local
```

Restart Apache.

## 14. Open application

Customer storefront:

`http://gutreset.local/`

Shop:

`http://gutreset.local/shop`

Login:

`http://gutreset.local/login`

Register:

`http://gutreset.local/register`

Customer account:

`http://gutreset.local/account`

Admin:

`http://gutreset.local/admin`

Admin payments:

`http://gutreset.local/admin/payments`

Admin fulfilment:

`http://gutreset.local/admin/fulfilment`

Admin WhatsApp:

`http://gutreset.local/admin/whatsapp`

Admin Day 0–30:

`http://gutreset.local/admin/reset-operations`

## 15. Alternative: Laravel development server

If Apache configuration is inconvenient during development:

```powershell
php artisan serve
```

Then use:

`http://127.0.0.1:8000`

This is optional. XAMPP Apache remains the intended local deployment method.

## 16. What works without external procurement

The local package is deliberately usable without Razorpay/Meta/courier credentials.

Available locally:

- Home page
- CMS-driven content paths
- Product catalogue
- Product page
- Cart
- Checkout UI
- Customer registration/login
- Customer account
- Order screens
- Payment UI
- Payment service structure
- Admin dashboard
- Product administration
- Inventory
- Coupons
- Shipping configuration
- CMS/media administration
- Payment/reconciliation UI
- Fulfilment dashboard
- Shipment tracking UI
- WhatsApp template administration
- WhatsApp workflow dashboard
- Day 0–30 operational dashboard
- Phase 2 API routes
- Local demo/reference data

External credentials are intentionally blank.

## 17. What will remain disabled until Phase 3 procurement/configuration

### Razorpay

Required later:

```env
RAZORPAY_KEY_ID=
RAZORPAY_KEY_SECRET=
RAZORPAY_WEBHOOK_SECRET=
```

### Meta WhatsApp Cloud API

Required later:

```env
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_ACCESS_TOKEN=
WHATSAPP_VERIFY_TOKEN=
WHATSAPP_APP_SECRET=
```

### Courier/shipment provider

Current local provider:

`manual`

A real provider adapter can be selected in Phase 3.

### Redis

Local setup uses:

`QUEUE_CONNECTION=sync`

Production can move to Redis without changing the business services/jobs.

## 18. Recommended UI finalisation order

1. Home page
2. Product listing
3. Product details
4. Cart
5. Checkout
6. Customer login/register
7. Customer account
8. Order tracking
9. Admin dashboard
10. Product management
11. Inventory
12. Shipping
13. CMS/media
14. Payment dashboard
15. Fulfilment dashboard
16. WhatsApp dashboard
17. Day 0–30 dashboard

Only after these are signed off should Phase 3 external integrations be configured.
