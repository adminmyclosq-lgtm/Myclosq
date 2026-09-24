# Myclosq — Phase 1C + Phase 2A/2B/2C/2D Local Project

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
http://myclosq.local
```

## Demo credentials

Admin:

`admin@myclosq.local`

`ChangeMe!123`

Customer:

`customer@myclosq.local`

`ChangeMe!123`

Change these if the environment is exposed beyond localhost.

## Reset journey integration — Day 0–30 + re-entry

The integrated user journey is now available under `/reset` and keeps the existing authentication, commerce and account framework intact.

### User workflow

```text
Product delivery / QR activation
              ↓
           Day 0
              ↓
Days 1 → 2 → 3 → 7 → 14 → 21 → 30
              ↓
Daily capsule tracking + milestone checkpoints
              ↓
Day 30 final review
              ↓
GRI + GRS calculation
              ↓
Individual Gut Response Brief
              ↓
Cycle status = completed
              ↓
Later: same login → Re-entry request → Admin approval → new cycle
```

The user pages are:

- `/reset` — current cycle home, 30-day progress and cycle history
- `/reset/day0` — baseline, priority areas, triggers, five gut-signal scores, start date and reminder preferences
- `/reset/day/{day}` — Day 1–30 daily check-in; milestone pages collect the Day 3/7/14/21/30 signal checkpoint and Day 30 final review
- `/reset/report/{cycle?}` — individual cycle report and historical cycle access
- `/reset/testimonial` — post-completion testimonial capture
- `/reset/re-entry` — same-account request for a later cycle

### Operational workflow

Unusual events can create a safety flag and pause automation until an administrator resolves the flag. Missed check-ins are detected by the scheduled workflow and move the profile to `dropoff`; WhatsApp reactivation prompts can record the return without deleting the existing cycle data.

The Day 30 scoring rules currently implemented are:

- `GRI = (capsules taken × 2) + (Day 30 comfort × 4)`, capped at 100.
- GRI bands: `80-100`, `60-79`, `40-59`, `0-39`.
- `GRS movement = Day 0 symptom burden − Day 30 symptom burden`, where symptom burden is bloating + gas/burping + heaviness + acidity.
- `Comfort delta = Day 30 comfort − Day 0 comfort`.

A completed cycle is retained. The account is not recreated for re-entry; administrator approval creates a new `reset_profiles` cycle linked to the previous cycle via `parent_reset_profile_id`.

### Database change

A supplemental migration has been added at:

`database/migrations/phase1c/20260919130000_add_reset_cycles_and_reentry_requests.php`

For a fresh Laravel migration run, use the existing Phase 1C path followed by the new migration. For an already provisioned database, apply the new migration after confirming the existing Phase 1C migrations are recorded in the `migrations` table.

For a database managed directly from the SQL authority, the equivalent one-time patch is `database/schema/reset_workflow_v1_patch.sql`. Use either the Laravel migration or the direct SQL patch, not both.

### WhatsApp

The existing Meta WhatsApp queue is reused. Added programme templates include `reactivation_prompt` and `day30_review`. Meta template approval and valid WhatsApp credentials remain deployment requirements; the project does not embed production secrets.

## Phase 3 is intentionally not configured

The local package contains no production secrets. Razorpay, Meta WhatsApp, courier/shipment provider and Redis production configuration remain blank/disabled for the UI finalisation stage.
