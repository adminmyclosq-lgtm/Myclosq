<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sign in - Guided Wellness' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-[var(--ink)]">
    <x-site-header />

    <main>
        @yield('content')
    </main>

    <x-site-footer :minimal="true" />
</body>
</html>