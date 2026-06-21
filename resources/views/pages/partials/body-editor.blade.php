{{-- Body block editor (Phase 4). Bound to the `body` Alpine array. --}}
<section class="rounded-xl border border-slate-200 bg-white">
    <div class="border-b border-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700">Indhold</div>

    <div class="space-y-1 p-4 pl-12">
        <p x-show="!body.length" class="py-8 text-center text-sm text-slate-400">
            Ingen blokke endnu — tilføj din første nedenfor.
        </p>

        <template x-for="(block, index) in body" :key="block.id">
            <div class="group relative rounded-lg border border-transparent px-3 py-2 hover:border-slate-200"
                 @dragover.prevent @drop.prevent="onDrop(index)">

                {{-- left rail: drag handle + type --}}
                <div class="absolute -left-9 top-2 flex flex-col items-center gap-1">
                    <button type="button" draggable="true" @dragstart="onDragStart(index)"
                            class="cursor-grab text-slate-300 opacity-0 transition group-hover:opacity-100 hover:text-slate-500" title="Træk">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 11-4 0 2 2 0 014 0zM8 12a2 2 0 11-4 0 2 2 0 014 0zM6 20a2 2 0 100-4 2 2 0 000 4zM20 6a2 2 0 11-4 0 2 2 0 014 0zM18 14a2 2 0 100-4 2 2 0 000 4zM20 18a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </button>
                </div>

                {{-- top-right: move/delete --}}
                <div class="absolute right-2 top-2 z-10 flex gap-0.5 opacity-0 transition group-hover:opacity-100">
                    <button type="button" @click="moveBlock(index, -1)" class="grid h-7 w-7 place-items-center rounded-md text-slate-400 hover:bg-slate-100" title="Op">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <button type="button" @click="moveBlock(index, 1)" class="grid h-7 w-7 place-items-center rounded-md text-slate-400 hover:bg-slate-100" title="Ned">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button type="button" @click="removeBlock(index)" class="grid h-7 w-7 place-items-center rounded-md text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Slet">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- ===== block bodies ===== --}}

                {{-- heading --}}
                <template x-if="block.type === 'heading'">
                    <div class="flex items-center gap-2 pr-24">
                        <select x-model.number="block.level" class="rounded-lg border-slate-200 py-1 text-xs">
                            <option :value="2">H2</option><option :value="3">H3</option><option :value="4">H4</option>
                        </select>
                        <input type="text" x-model="block.text" placeholder="Overskrift"
                               class="flex-1 border-0 bg-transparent p-0 text-2xl font-bold text-slate-900 placeholder:text-slate-300 focus:ring-0">
                    </div>
                </template>

                {{-- rich text --}}
                <template x-if="block.type === 'text'">
                    <div class="pr-24">
                        <div class="mb-1.5 flex gap-1 text-slate-500">
                            <button type="button" @mousedown.prevent="format('bold')" class="h-7 w-7 rounded font-bold hover:bg-slate-100">B</button>
                            <button type="button" @mousedown.prevent="format('italic')" class="h-7 w-7 rounded italic hover:bg-slate-100">I</button>
                            <button type="button" @mousedown.prevent="format('insertUnorderedList')" class="h-7 w-7 rounded hover:bg-slate-100">•</button>
                            <button type="button" @mousedown.prevent="format('insertOrderedList')" class="h-7 w-7 rounded text-xs hover:bg-slate-100">1.</button>
                            <button type="button" @mousedown.prevent="formatLink()" class="h-7 w-8 rounded text-xs hover:bg-slate-100">link</button>
                        </div>
                        <div contenteditable
                             x-init="$el.innerHTML = block.html || ''"
                             @input="block.html = $event.target.innerHTML"
                             class="prose prose-slate max-w-none min-h-[3rem] rounded-lg border border-slate-200 p-3 focus:border-brand focus:outline-none"></div>
                    </div>
                </template>

                {{-- image --}}
                <template x-if="block.type === 'image'">
                    <div class="pr-24">
                        <template x-if="block.media">
                            <div>
                                <img :src="block.media.url" :alt="block.media.alt" class="max-h-64 rounded-lg border border-slate-200">
                                <div class="mt-2 flex gap-3 text-sm">
                                    <button type="button" @click="pickMedia({type:'image'}, m => block.media = m)" class="text-brand-700 hover:underline">Skift</button>
                                    <button type="button" @click="block.media = null" class="text-rose-600 hover:underline">Fjern</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="!block.media">
                            <button type="button" @click="pickMedia({type:'image'}, m => block.media = m)"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-300 py-10 text-sm text-slate-500 hover:border-brand hover:text-brand-700">
                                Vælg billede
                            </button>
                        </template>
                    </div>
                </template>

                {{-- gallery --}}
                <template x-if="block.type === 'gallery'">
                    <div class="flex flex-wrap gap-2 pr-24">
                        <template x-for="(g, gi) in block.items" :key="gi">
                            <div class="relative">
                                <img :src="g.url" class="h-20 w-20 rounded-lg border border-slate-200 object-cover">
                                <button type="button" @click="block.items.splice(gi, 1)"
                                        class="absolute -right-1.5 -top-1.5 grid h-5 w-5 place-items-center rounded-full bg-rose-600 text-xs text-white">✕</button>
                            </div>
                        </template>
                        <button type="button" @click="pickMedia({type:'image'}, m => block.items.push(m))"
                                class="grid h-20 w-20 place-items-center rounded-lg border-2 border-dashed border-slate-300 text-slate-400 hover:border-brand hover:text-brand-700">+</button>
                    </div>
                </template>

                {{-- video --}}
                <template x-if="block.type === 'video'">
                    <div class="space-y-2 pr-24">
                        <input type="url" x-model="block.url" placeholder="YouTube-/Vimeo-URL"
                               class="w-full rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
                        <div class="flex items-center gap-2 text-sm">
                            <button type="button" @click="pickMedia({type:'video'}, m => { block.media = m; block.url = ''; })" class="text-brand-700 hover:underline">Vælg video-fil</button>
                            <span x-show="block.media" class="text-xs text-slate-500" x-text="block.media?.name"></span>
                            <button type="button" x-show="block.media" @click="block.media = null" class="text-xs text-rose-600 hover:underline">Fjern</button>
                        </div>
                    </div>
                </template>

                {{-- button --}}
                <template x-if="block.type === 'button'">
                    <div class="flex flex-wrap items-center gap-2 pr-24">
                        <input x-model="block.label" placeholder="Knaptekst" class="rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
                        <input x-model="block.href" placeholder="URL" class="flex-1 rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
                        <select x-model="block.style" class="rounded-lg border-slate-200 text-sm">
                            <option value="primary">Primær</option>
                            <option value="secondary">Sekundær</option>
                        </select>
                    </div>
                </template>

                {{-- two columns --}}
                <template x-if="block.type === 'columns'">
                    <div class="grid grid-cols-2 gap-3 pr-24">
                        <div contenteditable x-init="$el.innerHTML = block.left || ''" @input="block.left = $event.target.innerHTML"
                             class="prose prose-sm max-w-none min-h-[4rem] rounded-lg border border-slate-200 p-2.5 focus:border-brand focus:outline-none"></div>
                        <div contenteditable x-init="$el.innerHTML = block.right || ''" @input="block.right = $event.target.innerHTML"
                             class="prose prose-sm max-w-none min-h-[4rem] rounded-lg border border-slate-200 p-2.5 focus:border-brand focus:outline-none"></div>
                    </div>
                </template>

                {{-- divider --}}
                <template x-if="block.type === 'divider'">
                    <div class="py-2 pr-24"><hr class="border-slate-200"></div>
                </template>

                {{-- contact form --}}
                <template x-if="block.type === 'form'">
                    <div class="space-y-2 rounded-lg bg-slate-50 p-3 pr-24">
                        <input x-model="block.heading" placeholder="Overskrift" class="w-full rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
                        <p class="text-xs text-slate-400">Viser en kontaktformular (navn, e-mail, besked) på siden.</p>
                    </div>
                </template>

                {{-- insert-after --}}
                <div class="mt-1 opacity-0 transition group-hover:opacity-100">
                    @include('webkraft::pages.partials.add-block-menu', [
                        'at' => 'index + 1',
                        'label' => 'Indsæt under',
                        'btnClass' => 'inline-flex items-center gap-1 rounded px-2 py-1 text-xs text-slate-400 hover:text-brand-700',
                    ])
                </div>
            </div>
        </template>

        {{-- append --}}
        <div class="pt-2">
            @include('webkraft::pages.partials.add-block-menu', ['at' => 'null'])
        </div>
    </div>
</section>
