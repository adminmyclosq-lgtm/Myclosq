# Phase 2 API Contract

## Customer

### Auth
POST `/api/v1/auth/register`
POST `/api/v1/auth/login`
GET `/api/v1/me`
POST `/api/v1/auth/logout`

### Catalogue
GET `/api/v1/home`
GET `/api/v1/products`
GET `/api/v1/products/{id}`

### Cart
GET `/api/v1/cart`
POST `/api/v1/cart/items`
PATCH `/api/v1/cart/items/{cartItem}`
DELETE `/api/v1/cart/items/{cartItem}`

### Orders
GET `/api/v1/orders`
POST `/api/v1/orders`
GET `/api/v1/orders/{order}`

### Reset
GET `/api/v1/reset/profile`
POST `/api/v1/reset/checkins`

## WhatsApp

GET `/api/v1/webhooks/whatsapp` — Meta webhook verification.
POST `/api/v1/webhooks/whatsapp` — incoming event receiver.

## Admin

GET `/api/v1/admin/dashboard`
GET/POST/PUT `/api/v1/admin/products`
GET/PUT `/api/v1/admin/orders`
GET/PUT `/api/v1/admin/cms/pages`

## Business-layer rule

Controllers validate and delegate. Pricing, cart, checkout, reset and external integration logic belongs in services. Payment and shipment providers must be implemented behind interfaces before production.
