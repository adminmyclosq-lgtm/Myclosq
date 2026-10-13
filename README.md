#  Gut Reset — Platform Guide & Documentation


Welcome to the **Gut Reset** project! 

This guide is written so that any developer — whether you're new to Laravel or joining this project for the first time—can quickly understand how the platform works, how the code is structured, and how to get it running locally.

Gut Reset is an health-focused e-commerce platform and guided wellness programme built for gut health improvement and overall wellness.It helps users to improve their gut health and overall wellness through a 30-day guided programme.
---

## Table of Contents

1. [Project Overview](#-1-project-overview)
2. [Tech Stack](#-2-tech-stack)
3. [Quick Start (Local Setup)](#-3-quick-start-local-setup)
4. [Folder Structure](#-4-folder-structure)
5. [Page Directory & Navigation](#-5-page-directory--navigation)
   - [Public Pages (No Login)](#51-public-pages-guest)
   - [Customer Pages (After Login)](#52-customer-portal-logged-in-user)
   - [Admin Portal (Back-Office)](#53-admin-portal-after-login-as-admin)
6. [Database Schema & Architecture](#-6-database-schema--architecture)
7. [Core Business Logic (Services)](#-7-core-business-logic-services)
8. [Integrations (Razorpay & WhatsApp)](#-8-integrations)
9. [Developer Notes & Useful Files](#-9-developer-notes--useful-files)

---

1. Project Overview:

### The Customer Journey in 5 Steps:

[Home Page] ---> [Purchase / Buy gut capsule / Shop] ---> [Delivery / QR Scan for purchase and 30day cycle activataion] ---> [Day0(Initial gut survey)] ---> [Activates 30-day cycle / Day 1 - 29 Daily Check-ins] ---> [Day 30 Review & Scores] ---> [GRI / GRS scores calculated / Show recommendations] ---> [Maintenance guidelines or request admin approval to start a new cycle]

---
1. **E-Commerce Shop**: Customers browse and purchase gut health capsule bottles via a streamlined cart and checkout powered by **Razorpay**.
2. **30-Day Reset Programme Activation**: Once an order is delivered (or when a customer scans a physical product QR code), their 30-day guided reset journey is activated.
3. **Day 0 (Baseline Intake)**: The user completes an initial health survey recording starting symptoms, health goals, triggers, and WhatsApp reminder preferences.
4. **Days 1 to 29 (Daily Tracking)**: Every day, the user records:
   - Did they take their daily capsules?
   - Any digestive symptoms or discomfort experienced?
   - Any cheat meals or unusual life events?
5. **Day 30 (Review & Scoring)**: On Day 30, the platform automatically evaluates progress using two proprietary formulas:
   - **GRI (Gut Reset Index)**: A composite adherence & comfort score (out of 100):
     $$\text{GRI} = (\text{Capsules Taken} \times 2) + (\text{Day 30 Comfort Rating} \times 4)$$
   - **GRS (Gut Reset Score)**: Measures net symptom burden change from Day 0 to Day 30.
6. **Cycle Completion & Re-entry**: Successful users receive maintenance guidelines or can request admin approval to start a new reset cycle.
7. **WhatsApp Automation**: Meta WhatsApp API sends automated morning reminders and Day 30 check-ins.
8. **Admin Operations Back-Office**: Staff manage products, inventory, orders, customer shipments, reset approvals, CMS content, and WhatsApp templates.

---

## 2. Tech Stack

| Layer               | Technology                          |
|---------------------|-------------------------------------|
| **Backend**         | **PHP 8.3 + Laravel 13**            | 
| **Frontend**        | **Blade Templates**                 | 
| **Styling**         | **Vanilla CSS + Tailwind CSS v4**   | 
| **Interactivity**   | **Vanilla JavaScript + Axios**      | 
| **Build Pipeline**  | **Vite 7**                          | 
| **Database**        | **MySQL 8.0 / MariaDB**             | 
| **Authentication**  | **Laravel Session Auth + Sanctum**  | 
| **Payments**        | **Razorpay**                        | 
| **WhatsApp API**    | **Meta Cloud API**                  | 
| **Local Server**    | **XAMPP (Apache + MySQL)**          | 

---

## 4. Folder Structure

Here is where the most important files live:
gutreset/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/              # Public & Customer controllers (Cart, Checkout, Journey, etc.)
│   │   │   ├── Web/Admin/        # Admin portal controllers (Products, Orders, Reset, etc.)
│   │   │   └── Api/              # REST API controllers & webhooks (Razorpay, WhatsApp)
│   │   └── Middleware/
│   │       └── AdminPermission.php # Protects admin routes by role/permission
│   ├── Models/                   # 40+ Eloquent models representing database tables
│   └── Services/                 # 21 Service classes housing all business logic
├── database/
│   ├── migrations/
│   │   ├── phase1c/              # 67 raw-SQL migration files creating core tables
│   │   └── phase2_infrastructure/# Sanctum personal access tokens
│   └── database_dump.sql         # Authoritative database snapshot
├── docs/                         # Specifications & workflow documentation
├── resources/
│   ├── css/ & js/                # Stylesheets and JavaScript files
│   └── views/                    # Blade templates
│       ├── layouts/              # Base layouts (app, admin, course, auth)
│       ├── web/                  # Public and customer pages
│       └── admin/                # Admin back-office pages
└── routes/
    ├── web.php                   # All browser web routes (Public, User, Admin)
    └── api.php                   # REST API and webhook endpoints``
---

## 5. Page Directory & Navigation

### 5.1 Public Pages (Guest)
Accessible by anyone without logging in.
| Page Name     | URL Route | Blade Template       | Controller       |
| What It Does |
| **Home Page** | `/`       | `web/home.blade.php` | `HomeController` | 
|Hero Section | Value Proposition | Featured Products | Testimonials | CTA Section |
| **Shop**        | `/shop`   | `web/shop.blade.php` | `ShopController` | 
|Product catalog with variant selectors and pricing |
| **Product Detail**| `/product/{slug}` | `web/product-detail.blade.php` | `ShopController` | Detailed product page with ingredients, benefits, reviews, and "Add to Cart" |
| **Cart**        | `/cart`   | `web/cart.blade.php` | `CartController` | View items in cart, update quantities, enter coupons |
| **Checkout**    | `/checkout` | `web/checkout.blade.php` | `CheckoutController` | Delivery address form, order summary, Razorpay payment button |
| **Order Confirmation**| `/order/confirmed/{id}` | `web/order-confirmed.blade.php` | `CheckoutController` | Thank-you receipt with order number and tracking links |
| **Order Tracking** | `/track-order` | `web/track-order.blade.php` | `OrderTrackingController`| Look up shipment status using order number or phone |
| **QR Activation** | `/qr/{code}` | `web/qr-activate.blade.php` | `QrActivationController` | Scan product packaging QR code to start/link reset cycle |
| **Login / Register** | `/login`, `/register` | `auth/login.blade.php` | `AuthController` | Customer account login and registration |
| **Password Reset** | `/forgot-password` | `auth/forgot-password.blade.php`| `ForgotPasswordController`| Request email password reset link |

---

### 5.2 Customer Portal (Logged-In User)
Accessible by customers after logging in.

| Area / Page | URL Route | Blade Template | What It Does |
|---|---|---|---|
| **Customer Dashboard** | `/dashboard` | `web/dashboard.blade.php` | Overview of current orders, reset status, and account shortcuts |
| **Reset Hub** | `/reset` | `web/reset/hub.blade.php` | Central page showing current day, progress bar, and today's action item |
| **Day 0 Baseline** | `/reset/day-0` | `web/reset/day-0.blade.php` | Starting health survey: symptoms, goals, triggers, and disclaimer |
| **Daily Log (Days 1–29)**| `/reset/day/{day}` | `web/reset/daily-log.blade.php`| Daily check-in: capsule intake, digestion rating, symptoms, cheat meal notes |
| **Day 30 Review** | `/reset/day-30` | `web/reset/day-30.blade.php` | Final review survey: comfort rating, symptom improvements |
| **Reset Results** | `/reset/results` | `web/reset/results.blade.php` | Displays calculated **GRI** and **GRS** scores, outcome badges, and care plan |
| **Maintenance Plan** | `/reset/maintenance` | `web/reset/maintenance.blade.php`| Lifestyle, dietary, and habit guide for sustaining results after Day 30 |
| **Re-entry Request** | `/reset/reentry` | `web/reset/reentry.blade.php` | Apply for a second reset cycle (requires admin approval) |
| **Order History** | `/account/orders` | `web/account/orders.blade.php` | View past purchases, order status, invoices, and re-order |
| **Addresses** | `/account/addresses`| `web/account/addresses.blade.php`| Manage shipping and billing addresses |
| **Profile Settings** | `/account/profile` | `web/account/profile.blade.php` | Update name, email, phone number, and password |

---

### 5.3 Admin Portal (After Login as Admin)
Accessible at `/admin/*` by authenticated staff members with assigned roles.

| Admin Module | URL Route | Blade Template | Controller | What You Can Do |
|---|---|---|---|---|
| **Admin Dashboard** | `/admin` | `admin/dashboard.blade.php` | `DashboardController` | Sales revenue, orders count, active reset cycles, and alerts |
| **Products** | `/admin/products` | `admin/products/index.blade.php` | `ProductController` | Create, edit, and delete products, images, and prices |
| **Product Variants** | `/admin/products/{id}/variants` | `admin/products/variants.blade.php` | `ProductVariantController` | Manage packaging sizes, capsule counts, and SKUs |
| **Inventory** | `/admin/inventory` | `admin/inventory/index.blade.php` | `InventoryController` | Stock counts, stock adjustments, low-stock warnings, audit logs |
| **Orders** | `/admin/orders` | `admin/orders/index.blade.php` | `OrderController` | View orders, filter by status, cancel/refund, export CSV |
| **Shipments** | `/admin/shipments` | `admin/shipments/index.blade.php` | `ShipmentController` | Assign courier, enter tracking IDs, update dispatch/delivery |
| **Coupons** | `/admin/coupons` | `admin/coupons/index.blade.php` | `CouponController` | Create percentage/flat discount codes with usage limits |
| **Reset Operations** | `/admin/reset/profiles` | `admin/reset/profiles.blade.php` | `ResetAdminController` | View all customer reset journeys, pause/resume, check safety flags |
| **Re-entry Approvals**| `/admin/reset/reentries`| `admin/reset/reentries.blade.php` | `ResetAdminController` | Review and approve/reject requests for starting subsequent cycles |
| **Customers** | `/admin/customers` | `admin/customers/index.blade.php` | `CustomerController` | Customer database, total spend, phone numbers, WhatsApp opt-in status |
| **CMS Pages** | `/admin/cms` | `admin/cms/index.blade.php` | `CmsController` | Edit marketing banners, homepage copy, and informational pages |
| **Media Library** | `/admin/media` | `admin/media/index.blade.php` | `MediaController` | Upload and organize image assets |
| **WhatsApp Messages**| `/admin/whatsapp` | `admin/whatsapp/index.blade.php` | `WhatsAppAdminController` | Manage pre-approved WhatsApp message templates and delivery logs |
| **Roles & Staff** | `/admin/roles` | `admin/roles/index.blade.php` | `RoleController` | Assign administrative roles and permissions to staff users |

---

## 6. Database Schema & Architecture

The database contains **67 core tables** built via raw SQL migrations in `database/migrations/phase1c/`. Here is the architecture broken down by domain:

### 1. Identity & Permissions
* **`users`**: Core user authentication (name, email, password, phone, role).
* **`customer_profiles`**: Extended shopper data (date of birth, gender, WhatsApp opt-in).
* **`roles`**, **`permissions`**, **`role_user`**, **`permission_role`**: Role-based access control (RBAC). 
  * *Roles include:* `SUPER_ADMIN`, `RESET_ADMIN`, `ORDER_ADMIN`, `FULFILMENT_ADMIN`, `MARKETING_ADMIN`, `CUSTOMER_SUPPORT`.

### 2. E-Commerce & Catalog
* **`products`**: Product details (name, slug, description, status).
* **`product_variants`**: Specific sellable SKUs (size, pack count, SKU, price, weight).
* **`product_images`**: Variant and product image references.
* **`categories`**, **`product_categories`**: Product classifications.
* **`inventory`**, **`inventory_transactions`**: Live stock balances and movement audit trails.

### 3. Shopping Cart, Orders & Payments
* **`carts`**, **`cart_items`**: Transient and guest cart persistence.
* **`orders`**, **`order_items`**: Placed customer orders with billing/shipping snapshots.
* **`order_status_history`**: Audit log of order state changes (`pending` -> `processing` -> `shipped` -> `delivered`).
* **`payment_transactions`**: Razorpay transaction tracking (order ID, payment ID, signature, status).
* **`coupons`**, **`coupon_usages`**: Promo codes, rules, and usage logs.
* **`shipping_methods`**, **`shipments`**, **`shipment_tracking_events`**: Couriers, tracking numbers, delivery checkpoints.

### 4. The 30-Day Reset Programme (Core Domain)
* **`reset_profiles`**: The master record for a user's reset journey. Tracks `cycle_number`, `current_reset_day` (0–30), and `status` (`created` -> `active` -> `started` -> `in_progress` -> `paused` -> `completed`).
* **`day0_baselines`**: Baseline intake recorded on Day 0 (symptom ratings, regularity, expectations).
* **`day0_priority_areas`**, **`day0_triggers`**: Focus areas (bloating, energy, digestion) and lifestyle triggers.
* **`daily_logs`**: Days 1–29 daily submissions (capsules taken, digestive comfort rating).
* **`daily_log_symptoms`**: Specific symptoms experienced on that day.
* **`daily_log_cheat_meals`**, **`daily_log_life_events`**: Lifestyle disruptions logged on that day.
* **`day30_reviews`**: Day 30 final check-in survey.
* **`reset_scores`**: Calculated final metrics (**GRI** and **GRS**), outcome category, and clinical recommendations.
* **`reset_reentry_requests`**: Applications by users to begin another 30-day reset cycle.

### 5. Communications & CMS
* **`whatsapp_contacts`**, **`whatsapp_messages`**, **`whatsapp_webhook_logs`**: Meta WhatsApp integration tracking.
* **`cms_pages`**, **`cms_sections`**: Editable website content.
* **`testimonials`**, **`reviews`**: Customer reviews and moderation flags.

---

## ⚙️ 7. Core Business Logic (Services)

Business logic is intentionally separated from controllers into **21 dedicated Service classes** in `app/Services/`:

| Service Class | File Location | Responsibility |
|---|---|---|
| `ResetJourneyService` | `app/Services/ResetJourneyService.php` | Advances reset days, validates daily log completion, triggers Day 30 evaluation |
| `ResetScoringService` | `app/Services/ResetScoringService.php` | Calculates **GRI** and **GRS** scores based on logs and reviews |
| `ResetReentryService` | `app/Services/ResetReentryService.php` | Handles applications, eligibility checks, and approvals for subsequent cycles |
| `OrderService` | `app/Services/OrderService.php` | Creates orders from carts, calculates totals, applies coupon discounts |
| `InventoryService` | `app/Services/InventoryService.php` | Reserves stock during checkout, adjusts stock on shipment or cancellation |
| `RazorpayService` | `app/Services/RazorpayService.php` | Creates Razorpay order IDs, verifies payment cryptographic signatures |
| `WhatsAppService` | `app/Services/WhatsAppService.php` | Sends WhatsApp messages via Meta Graph API, logs message status |
| `GutResetWhatsAppService`| `app/Services/GutResetWhatsAppService.php`| Dispatches journey-specific WhatsApp notifications (daily reminders, prompts) |
| `QrCodeService` | `app/Services/QrCodeService.php` | Resolves product QR codes to customer accounts and triggers journey onboarding |

---

## 8. Integrations

###  Razorpay Payments
1. During checkout, `CheckoutController` calls `RazorpayService::createOrder()` to generate an order on Razorpay.
2. The user pays using the Razorpay JavaScript Modal.
3. Upon payment completion, Razorpay returns a `razorpay_payment_id` and `razorpay_signature`.
4. `CheckoutController::verifyPayment()` cryptographically verifies the signature before marking the order as `PAID`.
5. Webhook listener at `/api/razorpay/webhook` (`RazorpayWebhookController`) serves as a backup to capture asynchronous payments.

###  Meta WhatsApp Cloud API
1. When users check the WhatsApp opt-in checkbox on Day 0, `whatsapp_contacts.opt_in` is marked `true`.
2. Daily reminder jobs invoke `GutResetWhatsAppService` to deliver the morning check-in template.
3. Webhook listener at `/api/whatsapp/webhook` (`WhatsAppWebhookController`) validates the Meta webhook token and logs customer replies.

---

##  9. Developer Notes & Useful Files

* **Admin Route Middleware**: All admin routes in `routes/web.php` are protected by `AdminPermission` middleware (`app/Http/Middleware/AdminPermission.php`), which checks roles defined in the `roles` table.
* **Vite Compilation**: CSS and JS are bundled using Vite. Run `npm run dev` during local development or `npm run build` for production assets.
* **Detailed Workflow Specs**: For in-depth clinical rules, score thresholds, and detailed flowcharts, read:
  - [`docs/RESET_WORKFLOW_V1.md`](file:///c:/xampp/htdocs/gutreset/docs/RESET_WORKFLOW_V1.md)
* **Database Dump**: A complete, working database export is preserved in:
  - [`database_dump.sql`](file:///c:/xampp/htdocs/gutreset/database_dump.sql)

---

*Enjoy working on the Gut Reset platform! If you encounter any questions, refer to the service classes or check the `docs/` folder.*