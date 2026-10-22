<!doctype html>
<html lang="en" class="scroll-smooth scroll-pt-20">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview: {{ $page->title }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" data-precedence="default" />
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        /* Disable external link navigations in preview iframe so user doesn't accidentally navigate away */
        a { pointer-events: auto; }
        .cms-preview-banner {
            position: fixed;
            bottom: 12px;
            right: 12px;
            z-index: 9999;
            background: rgba(26, 46, 38, 0.92);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 9999px;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 6px;
            pointer-events: none;
        }
        .cms-preview-banner span.dot {
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    <x-site-header />

    <main>
        <x-cms-page-renderer :page="$page" :is-preview="true" :products="$products ?? null" />
    </main>

    <x-site-footer />

    <div class="cms-preview-banner">
        <span class="dot"></span> Live Studio Preview
    </div>
</body>
</html>
