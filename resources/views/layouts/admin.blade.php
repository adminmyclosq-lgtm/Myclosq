<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Gut Reset Admin' }}</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-stone-50">
<div class="min-h-screen md:flex">
<aside class="w-full border-r bg-white md:w-64">
<div class="p-6 text-xl font-bold">Gut Reset Admin</div>
<nav class="space-y-1 px-4 pb-6 text-sm">
@foreach([
['Dashboard','admin.dashboard'],['Products','admin.products.index'],['Orders','admin.orders.index'],['Customers','admin.customers.index'],
['Inventory','admin.inventory.index'],['Shipping','admin.shipping.index'],['Coupons','admin.coupons.index'],['Media','admin.media.index'],['CMS','admin.cms.index'],['Payments','admin.payments.index'],['Fulfilment','admin.fulfilment.index'],['WhatsApp','admin.whatsapp.dashboard'],['Day 0–30','admin.reset.operations']
] as $item)
<a class="block rounded-xl px-4 py-3 hover:bg-stone-100" href="{{ route($item[1]) }}">{{ $item[0] }}</a>
@endforeach
<a class="mt-5 block rounded-xl px-4 py-3 bg-stone-900 text-white" href="{{ route('home') }}">View storefront</a>
</nav>
</aside>
<main class="flex-1">@yield('content')</main>
</div>
</body>
</html>
