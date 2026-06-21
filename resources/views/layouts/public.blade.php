@php
    $brand = \Webkraft\Cms\Support\Branding::tokens();
@endphp
<!DOCTYPE html>
<html lang="da" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $brand['name'])</title>
    @hasSection('meta_description')<meta name="description" content="@yield('meta_description')">@endif

    {{-- Phase 6 swaps this CDN for a compiled stylesheet shipped with the package. --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --wk-primary: {{ $brand['primary'] }}; --wk-container: {{ $brand['container'] }}; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .wk-container { max-width: var(--wk-container); }
        .wk-accent { color: var(--wk-primary); }
        .wk-accent-bg { background-color: var(--wk-primary); }
    </style>
    @stack('head')
</head>
<body class="h-full bg-white text-slate-800 antialiased flex min-h-full flex-col">
    @include('webkraft::partials.public-nav', ['brand' => $brand])

    <main class="flex-1">
        @yield('body')
    </main>

    <footer class="border-t border-slate-100 py-8">
        <div class="wk-container mx-auto px-6 text-sm text-slate-400">
            © {{ date('Y') }} {{ $brand['name'] }}
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
