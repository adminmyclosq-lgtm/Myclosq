# Gut Reset — Phase 1C + Phase 2A/2B/2C/2D Local Project

This is the complete source package for local UI/UX and functional finalisation before Phase 3 external integrations.

## Included

- **Phase 1C:** final 66-table MySQL 8 production schema, complete Laravel migration set, data dictionary and reference data.
- **Phase 2A:** customer authentication, customer account and role/permission foundation.
- **Phase 2B:** ecommerce catalogue, product pages, cart, checkout, orders, coupons, inventory, shipping and admin management.
- **Phase 2C:** payment gateway abstraction/Razorpay adapter, payment webhook, reconciliation, shipment creation, tracking webhook, order automation, Meta WhatsApp service/templates/webhooks, queued jobs and Day 0–30 workflow.
- **Phase 2D:** customer payment UI, order tracking, admin payment/reconciliation dashboard, fulfilment dashboard, WhatsApp workflow dashboard and Day 0–30 operations dashboard.
- **Local XAMPP support:** Apache public document root, MySQL 8 SQL import, local `.env`, demo users, local synchronous queue and exact setup guide.

## Database authority

`database/schema/phase1c_final_production_mysql.sql` is the Phase 1C SQL authority and contains 66 business tables.

`database/migrations/phase1c/` is the migration representation of the same Phase 1C schema.

> Note: Laravel does not scan nested migration subdirectories by default. Use `php artisan migrate --path=database/migrations/phase1c` or ensure the `migrations` table contains matching entries when the schema is imported directly.

`database/schema/phase1c_final_data_dictionary.csv` and `.md` contain the final data dictionary.

`database/schema/phase2_infrastructure.sql` and `database/migrations/phase2_infrastructure/` contain only the optional Laravel Sanctum framework table `personal_access_tokens`; this is not part of the 66-table business schema.

## Local deployment

Read:

`docs/local/LOCAL_XAMPP_SETUP.md`

The short sequence is:

```text
XAMPP Apache + MySQL
        ↓
composer install
        ↓
.env.xampp.example → .env
        ↓
php artisan key:generate
        ↓
Import Phase 1C SQL
        ↓
php artisan db:seed
        ↓
php artisan storage:link
        ↓
npm install
        ↓
npm run build
        ↓
Apache VirtualHost → public/
        ↓
http://gutreset.local
```

## Demo credentials

Admin:

`admin@gutreset.local`

`ChangeMe!123`

Customer:

`customer@gutreset.local`

`ChangeMe!123`

Change these if the environment is exposed beyond localhost.

## Phase 3 is intentionally not configured

The local package contains no production secrets. Razorpay, Meta WhatsApp, courier/shipment provider and Redis production configuration remain blank/disabled for the UI finalisation stage.
