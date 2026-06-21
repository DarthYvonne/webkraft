@extends('webkraft::layouts.admin')

@section('title', 'Sider')

@section('actions')
    <a href="{{ $webkraftBase }}/pages/create"
       class="inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Ny side
    </a>
@endsection

@push('head')
    <script defer src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endpush

@section('content')
@php $base = $webkraftBase; @endphp

@if ($pages->isEmpty())
    <div class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center text-sm text-slate-500">
        Ingen sider endnu. <a href="{{ $base }}/pages/create" class="font-medium text-brand-700 hover:underline">Opret den første</a>.
    </div>
@else
<div data-wk-pages-root>
    <ul data-sortable data-parent="" class="space-y-3">
        @foreach ($pages as $page)
            @include('webkraft::pages.partials.row', ['page' => $page])
        @endforeach
    </ul>
</div>

<script>
    (function () {
        const base = @js($base);
        function collect(list) {
            return Array.from(list.children)
                .filter(el => el.dataset.id)
                .map((el, i) => ({ id: +el.dataset.id, parent_id: list.dataset.parent ? +list.dataset.parent : null, sort_order: i }));
        }
        async function persist(list) {
            await fetch(base + '/pages/reorder', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': window.wkCsrf(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ items: collect(list) }),
            });
        }
        function init() {
            if (!window.Sortable) return setTimeout(init, 50);
            document.querySelectorAll('[data-sortable]').forEach(list => {
                Sortable.create(list, {
                    handle: '[data-handle]', animation: 150, ghostClass: 'opacity-40',
                    onEnd: () => persist(list),
                });
            });
        }
        init();
    })();
</script>
@endif
@endsection
