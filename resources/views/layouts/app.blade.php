<!doctype html>
<html lang="en" class="scroll-smooth scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gut Reset' }}</title>
    <script>
        window.AppRoutes = {
            cartItems: @json(url('/api/v1/cart/items')),
            cartItemBase: @json(url('/api/v1/cart/items')),
            login: @json(route('login')),
        };
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" data-precedence="default" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
    <x-site-header />

<main class="overflow-x-clip">
    @yield('content')
</main>

<x-site-footer />
</body>
</html>
