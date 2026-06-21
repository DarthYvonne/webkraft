{{-- Reusable "insert block" menu. $at = JS expression for insert position
     (e.g. 'null' to append, 'index + 1' to insert after current). --}}
<div x-data="{ open: false }" class="relative {{ $class ?? '' }}">
    <button type="button" @click="open = !open"
            class="{{ $btnClass ?? 'inline-flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 px-3 py-2 text-sm font-medium text-slate-500 hover:border-brand hover:text-brand-700' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span>{{ $label ?? 'Tilføj blok' }}</span>
    </button>
    <div x-show="open" x-cloak x-transition @click.outside="open = false"
         class="absolute left-0 z-20 mt-1 grid w-56 grid-cols-1 gap-0.5 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
        <template x-for="t in blockTypes" :key="t.type">
            <button type="button" @click="addBlock(t.type, {!! $at !!}); open = false"
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                <span x-text="t.label"></span>
            </button>
        </template>
    </div>
</div>
