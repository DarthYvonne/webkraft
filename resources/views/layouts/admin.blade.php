<!DOCTYPE html>
<html lang="da" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Webkraft') · Webkraft</title>

    {{-- Self-contained admin styling: Tailwind + Alpine via CDN so the package
         needs no build step in the host app. Public pages ship compiled CSS. --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { brand: { DEFAULT: '#4f46e5', 600: '#4f46e5', 700: '#4338ca' } },
                fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
            } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>[x-cloak]{display:none!important}</style>
    @include('webkraft::partials.scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">
@php
    $base = '/'.trim(config('webkraft.path', 'cms'), '/');
    $nav = [
        ['label' => 'Oversigt',     'href' => $base,              'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Sider',        'href' => $base.'/pages',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Mediearkiv',   'href' => $base.'/media',     'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Indstillinger','href' => $base.'/settings',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
    $current = '/'.trim(request()->path(), '/');
@endphp
<div class="flex min-h-full">
    {{-- Sidebar --}}
    <aside class="hidden md:flex w-60 flex-col border-r border-slate-200 bg-white">
        <div class="flex h-16 items-center gap-2 px-5 border-b border-slate-100">
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-brand text-white font-bold">W</div>
            <span class="font-semibold tracking-tight">Webkraft</span>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            @foreach ($nav as $item)
                @php $active = $item['href'] === $base ? $current === $base : str_starts_with($current, $item['href']); @endphp
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                          {{ $active ? 'bg-brand/10 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-slate-100 p-3">
            <a href="/" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Til websitet
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white px-6">
            <h1 class="text-lg font-semibold tracking-tight">@yield('heading', View::yieldContent('title', 'Oversigt'))</h1>
            <div class="flex items-center gap-3">
                @yield('actions')
            </div>
        </header>

        @if (session('status'))
            <div class="mx-6 mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-6 mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

@include('webkraft::partials.media-picker-modal')
</body>
</html>
