@php $menu = $menu ?? \Webkraft\Cms\Support\Nav::menu(); @endphp
<header x-data="{ mobile: false }" class="sticky top-0 z-30 border-b border-slate-100 bg-white/90 backdrop-blur">
    <div class="wk-container mx-auto flex h-16 items-center justify-between px-6">
        <a href="/" class="flex items-center gap-2">
            @if (!empty($brand['logo']))
                <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}" class="h-8 w-auto">
            @else
                <span class="text-lg font-extrabold tracking-tight">{{ $brand['name'] }}</span>
            @endif
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($menu as $item)
                @if ($item->children->isNotEmpty())
                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                        <a href="{{ $item->url() }}" class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                            {{ $item->title }}
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <div x-show="open" x-cloak x-transition.opacity
                             class="absolute left-0 top-full w-56 rounded-xl border border-slate-100 bg-white p-1.5 shadow-lg">
                            @foreach ($item->children as $child)
                                <a href="{{ $child->url() }}" class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">{{ $child->title }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->url() }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">{{ $item->title }}</a>
                @endif
            @endforeach
        </nav>

        <button @click="mobile = !mobile" class="md:hidden text-slate-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    {{-- Mobile nav --}}
    <div x-show="mobile" x-cloak class="border-t border-slate-100 md:hidden">
        <nav class="space-y-1 px-6 py-3">
            @foreach ($menu as $item)
                <a href="{{ $item->url() }}" class="block py-2 text-sm font-medium text-slate-700">{{ $item->title }}</a>
                @foreach ($item->children as $child)
                    <a href="{{ $child->url() }}" class="block py-1.5 pl-4 text-sm text-slate-500">{{ $child->title }}</a>
                @endforeach
            @endforeach
        </nav>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</header>
