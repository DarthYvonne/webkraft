@extends('webkraft::layouts.admin')

@section('title', 'Mediearkiv')

@section('actions')
    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Upload
        <input type="file" class="hidden" multiple accept="image/*,video/*" @change="upload($event.target.files); $event.target.value=''">
    </label>
@endsection

@section('content')
<div x-data="webkraftMediaLibrary(@js($items), '{{ $webkraftBase }}')"
     @dragover.prevent="dragging = true"
     @dragleave.prevent="dragging = false"
     @drop.prevent="dragging = false; upload($event.dataTransfer.files)"
     class="relative">

    {{-- Drop overlay --}}
    <div x-show="dragging" x-cloak
         class="pointer-events-none fixed inset-0 z-40 m-4 grid place-items-center rounded-2xl border-2 border-dashed border-brand bg-brand/5 text-brand-700">
        <span class="text-lg font-semibold">Slip filer for at uploade</span>
    </div>

    {{-- Toolbar --}}
    <div class="mb-4 flex items-center gap-3">
        <div class="relative flex-1 max-w-sm">
            <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Søg i mediearkiv…"
                   class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-brand focus:ring-brand">
        </div>
        <div class="flex rounded-lg border border-slate-200 bg-white p-0.5 text-sm">
            <template x-for="opt in [['all','Alle'],['image','Billeder'],['video','Video']]" :key="opt[0]">
                <button @click="filter = opt[0]" x-text="opt[1]"
                        :class="filter === opt[0] ? 'bg-slate-100 text-slate-900' : 'text-slate-500'"
                        class="rounded-md px-3 py-1 font-medium"></button>
            </template>
        </div>
        <span class="text-sm text-slate-400" x-text="filtered.length + ' filer'"></span>
    </div>

    {{-- Uploading placeholders --}}
    <div x-show="queue.length" class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <template x-for="u in queue" :key="u.id">
            <div class="aspect-square animate-pulse rounded-xl border border-slate-200 bg-slate-100 grid place-items-center text-xs text-slate-400" x-text="u.name"></div>
        </template>
    </div>

    {{-- Grid --}}
    <div x-show="!filtered.length && !queue.length" class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center text-sm text-slate-500">
        Ingen filer endnu. Træk filer hertil eller tryk <span class="font-medium text-slate-700">Upload</span>.
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <template x-for="item in filtered" :key="item.id">
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="aspect-square bg-slate-50">
                    <template x-if="item.type === 'image'">
                        <img :src="item.url" :alt="item.alt" class="h-full w-full object-cover" loading="lazy">
                    </template>
                    <template x-if="item.type === 'video'">
                        <div class="relative h-full w-full">
                            <video :src="item.url" class="h-full w-full object-cover" muted></video>
                            <div class="absolute inset-0 grid place-items-center bg-black/20">
                                <svg class="h-9 w-9 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- hover actions --}}
                <div class="absolute right-2 top-2 flex gap-1 opacity-0 transition group-hover:opacity-100">
                    <button @click="editing = item; altDraft = item.alt || ''"
                            class="grid h-7 w-7 place-items-center rounded-md bg-white/90 text-slate-600 shadow hover:text-brand-700" title="Rediger alt-tekst">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button @click="remove(item)"
                            class="grid h-7 w-7 place-items-center rounded-md bg-white/90 text-slate-600 shadow hover:text-rose-600" title="Slet">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                <div class="truncate px-2.5 py-2 text-xs text-slate-500" x-text="item.name"></div>
            </div>
        </template>
    </div>

    {{-- Alt-text editor --}}
    <div x-show="editing" x-cloak @keydown.escape.window="editing = null"
         class="fixed inset-0 z-50 grid place-items-center bg-slate-900/40 p-4">
        <div @click.outside="editing = null" class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="mb-3 font-semibold">Alt-tekst</h3>
            <template x-if="editing">
                <div class="mb-4 overflow-hidden rounded-lg border border-slate-200">
                    <img x-show="editing.type==='image'" :src="editing.url" class="max-h-48 w-full object-contain bg-slate-50">
                </div>
            </template>
            <input x-model="altDraft" type="text" placeholder="Beskriv billedet…"
                   class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
            <div class="mt-4 flex justify-end gap-2">
                <button @click="editing = null" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Annuller</button>
                <button @click="saveAlt()" class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Gem</button>
            </div>
        </div>
    </div>
</div>
@endsection
