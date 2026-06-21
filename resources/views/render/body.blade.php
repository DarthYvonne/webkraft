{{-- Public body renderer (Phase 4) — the full block set. --}}
@foreach (($blocks ?? []) as $block)
    @php $type = $block['type'] ?? null; @endphp
    @switch($type)

        @case('heading')
            @php $level = max(2, min(4, (int) ($block['level'] ?? 2))); @endphp
            <h{{ $level }}>{{ $block['text'] ?? '' }}</h{{ $level }}>
            @break

        @case('text')
            {!! $block['html'] ?? '' !!}
            @break

        @case('image')
            @if (!empty($block['media']['url']))
                <figure class="not-prose my-6">
                    <img src="{{ $block['media']['url'] }}" alt="{{ $block['media']['alt'] ?? '' }}"
                         @if(!empty($block['media']['width'])) width="{{ $block['media']['width'] }}" height="{{ $block['media']['height'] }}" @endif
                         class="mx-auto rounded-xl" loading="lazy">
                </figure>
            @endif
            @break

        @case('gallery')
            @if (!empty($block['items']))
                <div class="not-prose my-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($block['items'] as $img)
                        <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?? '' }}" class="aspect-square w-full rounded-lg object-cover" loading="lazy">
                    @endforeach
                </div>
            @endif
            @break

        @case('video')
            @php
                $url = $block['url'] ?? '';
                $embed = null;
                if ($url && preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/))([\w-]+)~', $url, $m)) {
                    $embed = "https://www.youtube.com/embed/{$m[1]}";
                } elseif ($url && preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
                    $embed = "https://player.vimeo.com/video/{$m[1]}";
                }
            @endphp
            <div class="not-prose my-6">
                @if ($embed)
                    <div class="aspect-video overflow-hidden rounded-xl">
                        <iframe src="{{ $embed }}" class="h-full w-full" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @elseif (!empty($block['media']['url']))
                    <video src="{{ $block['media']['url'] }}" controls class="w-full rounded-xl"></video>
                @endif
            </div>
            @break

        @case('button')
            @if (!empty($block['label']))
                @php $primary = ($block['style'] ?? 'primary') === 'primary'; @endphp
                <p class="not-prose my-6">
                    <a href="{{ $block['href'] ?: '#' }}"
                       class="inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold transition
                              {{ $primary ? 'wk-accent-bg text-white hover:opacity-90' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                        {{ $block['label'] }}
                    </a>
                </p>
            @endif
            @break

        @case('columns')
            <div class="not-prose my-6 grid gap-6 sm:grid-cols-2">
                <div class="prose prose-slate max-w-none">{!! $block['left'] ?? '' !!}</div>
                <div class="prose prose-slate max-w-none">{!! $block['right'] ?? '' !!}</div>
            </div>
            @break

        @case('divider')
            <hr class="my-8 border-slate-200">
            @break

        @case('form')
            @include('webkraft::partials.contact-form', ['heading' => $block['heading'] ?? 'Kontakt os', 'button' => $block['button'] ?? 'Send'])
            @break

    @endswitch
@endforeach
