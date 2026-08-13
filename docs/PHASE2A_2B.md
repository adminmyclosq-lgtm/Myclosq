# Phase 2A / 2B — Customer Authentication + Ecommerce + Admin

## Phase 2A
- Email/mobile registration.
- Password hashing using `password_hash`.
- Laravel session authentication.
- Sanctum API tokens.
- Customer role assignment.
- Customer profile creation.
- Login timestamp.
- Account dashboard.
- Logout/session invalidation.
- Admin role/permission middleware.

## Phase 2B
- Product catalogue.
- Product variants.
- Effective-dated pricing.
- Cart and price snapshots.
- Coupon validation.
- Shipping method/rate configuration.
- Checkout transaction.
- Inventory reservation.
- Order/item snapshots.
- Order status history.
- Payment data model ready.
- Shipment/tracking data model ready.
- Customer order history.
- Admin product management.
- Admin order/fulfilment management.
- Admin customer management.
- Admin inventory adjustment.
- Admin shipping configuration.
- Admin coupon/offer management.
- Admin media upload.
- Admin CMS page management.
- Sales analytics dashboard.

## Production boundary
Actual payment-gateway capture/refund and courier API calls are not falsely implemented as completed. Provider adapters must be configured with the chosen gateway/courier credentials and verified webhooks before production use.
