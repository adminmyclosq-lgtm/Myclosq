# Phase 2 Scope

## Implemented in this code package

1. Laravel 13 application structure.
2. MySQL 8 Phase 1C schema as canonical database source.
3. 66 Eloquent models.
4. 66 dependency-ordered table migrations.
5. Customer storefront Blade UI.
6. Catalogue/product detail UI.
7. Cart UI and cart API.
8. Customer authentication API with Sanctum.
9. Order/checkout service and API.
10. Guided reset check-in API and business service.
11. WhatsApp webhook verification and inbound event persistence.
12. WhatsApp outbound template service.
13. Admin dashboard API/UI.
14. Product/order/CMS admin API foundation.
15. Sales analytics service.
16. Payment gateway and shipment provider contracts.
17. API request validation and API resources.
18. Initial feature tests.

## Next Phase 2 increments

- Real payment gateway implementation and webhook verification.
- Shipment/courier adapter and tracking.
- OTP/WhatsApp login if required.
- Full admin CRUD for media/product pricing/offers/inventory.
- CMS section/image uploader UI.
- Complete RBAC middleware by permission code.
- Queue jobs for WhatsApp and notifications.
- Day 0 / Day 30 scoring engine and GRI/GRS calculation rules.
- Production observability, rate limiting and audit hardening.
