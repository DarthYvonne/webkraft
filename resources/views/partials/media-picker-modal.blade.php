{{-- Global, reusable media picker. Lives once in the admin layout.
     Open via: $store.mediaPicker.show(base, {type}, callback) --}}
<div x-data x-show="$store.mediaPicker.open" x-cloak
     @keydown.escape.window="$store.mediaPicker.close()"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 p-4">
    <div @click.outside="$store.mediaPicker.close()"
         class="flex h-[80vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-3">
            <h3 class="font-semibold">Vælg medie</h3>
            <div class="relative ml-auto w-64">
                <input x-model="$store.mediaPicker.search" @input="$store.mediaPicker.onSearch()"
                       type="search" placeholder="Søg…"
                       class="w-full rounded-lg border-slate-300 py-1.5 pl-3 text-sm focus:border-brand focus:ring-brand">
            </div>
            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-brand px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Upload
                <input type="file" class="hidden" multiple accept="image/*,video/*"
                       @change="$store.mediaPicker.uploadPicked($event.target.files); $event.target.value=''">
            </label>
            <button @click="$store.mediaPicker.close()" class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Grid --}}
        <div class="flex-1 overflow-y-auto p-5">
            <div x-show="$store.mediaPicker.loading" class="py-16 text-center text-sm text-slate-400">Indlæser…</div>
            <div x-show="!$store.mediaPicker.loading && !$store.mediaPicker.items.length" class="py-16 text-center text-sm text-slate-400">
                Ingen filer. Tryk Upload for at tilføje.
            </div>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5">
                <template x-for="item in $store.mediaPicker.items" :key="item.id">
                    <button @click="$store.mediaPicker.choose(item)"
                            class="group relative aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-50 ring-brand transition hover:ring-2">
                        <template x-if="item.type === 'image'">
                            <img :src="item.url" :alt="item.alt" class="h-full w-full object-cover" loading="lazy">
                        </template>
                        <template x-if="item.type === 'video'">
                            <div class="relative h-full w-full">
                                <video :src="item.url" class="h-full w-full object-cover" muted></video>
                                <div class="absolute inset-0 grid place-items-center bg-black/20">
                                    <svg class="h-8 w-8 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </template>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
