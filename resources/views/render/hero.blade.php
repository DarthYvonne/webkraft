{{-- Public hero renderer (Phase 5): image_bg / video_bg / split. --}}
@php $type = $hero['type'] ?? null; @endphp
@switch($type)

    @case('image_bg')
        @php $align = ($hero['align'] ?? 'center') === 'left' ? 'text-left' : 'text-center'; $center = ($hero['align'] ?? 'center') !== 'left'; @endphp
        <section class="relative isolate overflow-hidden">
            @if (!empty($hero['media']['url']))
                <img src="{{ $hero['media']['url'] }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
            @endif
            <div class="absolute inset-0 -z-10 bg-slate-900/50"></div>
            <div class="wk-container mx-auto px-6 py-28 text-white {{ $align }}">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $hero['headline'] ?: $page->title }}</h1>
                @if (!empty($hero['subhead']))
                    <p class="mt-4 max-w-2xl text-lg text-white/90 {{ $center ? 'mx-auto' : '' }}">{{ $hero['subhead'] }}</p>
                @endif
                @if (!empty($hero['button_label']))
                    <a href="{{ $hero['button_href'] ?: '#' }}" class="mt-7 inline-block rounded-lg bg-white px-6 py-3 font-semibold text-slate-900 transition hover:bg-white/90">{{ $hero['button_label'] }}</a>
                @endif
            </div>
        </section>
        @break

    @case('video_bg')
        <section class="relative isolate overflow-hidden">
            @if (!empty($hero['media']['url']))
                <video autoplay muted loop playsinline class="absolute inset-0 -z-10 h-full w-full object-cover">
                    <source src="{{ $hero['media']['url'] }}">
                </video>
            @endif
            <div class="absolute inset-0 -z-10 bg-slate-900/50"></div>
            <div class="wk-container mx-auto px-6 py-32 text-center text-white">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $hero['headline'] ?: $page->title }}</h1>
                @if (!empty($hero['subhead']))
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-white/90">{{ $hero['subhead'] }}</p>
                @endif
                @if (!empty($hero['button_label']))
                    <a href="{{ $hero['button_href'] ?: '#' }}" class="mt-7 inline-block rounded-lg bg-white px-6 py-3 font-semibold text-slate-900 transition hover:bg-white/90">{{ $hero['button_label'] }}</a>
                @endif
            </div>
        </section>
        @break

    @case('split')
        @php $imgRight = ($hero['side'] ?? 'right') === 'right'; @endphp
        <section class="wk-container mx-auto grid items-center gap-10 px-6 py-20 lg:grid-cols-2">
            <div class="{{ $imgRight ? 'lg:order-2' : 'lg:order-1' }}">
                @if (!empty($hero['media']['url']))
                    <img src="{{ $hero['media']['url'] }}" alt="{{ $hero['media']['alt'] ?? '' }}" class="w-full rounded-2xl object-cover">
                @endif
            </div>
            <div class="{{ $imgRight ? 'lg:order-1' : 'lg:order-2' }}">
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ $hero['headline'] ?: $page->title }}</h1>
                @if (!empty($hero['body']))
                    <div class="prose prose-slate mt-4 max-w-none">{!! $hero['body'] !!}</div>
                @endif
                @if (!empty($hero['button_label']))
                    <a href="{{ $hero['button_href'] ?: '#' }}" class="wk-accent-bg mt-6 inline-block rounded-lg px-6 py-3 font-semibold text-white transition hover:opacity-90">{{ $hero['button_label'] }}</a>
                @endif
            </div>
        </section>
        @break

    @default
        <header class="border-b border-slate-100 bg-slate-50">
            <div class="wk-container mx-auto px-6 py-16">
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ $page->title }}</h1>
            </div>
        </header>
@endswitch
