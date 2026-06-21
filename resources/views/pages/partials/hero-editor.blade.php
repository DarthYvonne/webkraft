{{-- Hero editor (Phase 5). Bound to the `hero` Alpine state. --}}
<section class="rounded-xl border border-slate-200 bg-white">
    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-2.5">
        <span class="text-sm font-medium text-slate-700">Hero</span>
        <select @change="setHeroType($event.target.value)" class="rounded-lg border-slate-200 py-1 text-sm">
            <option value="" :selected="!hero">Ingen</option>
            <option value="image_bg" :selected="hero?.type === 'image_bg'">Billede-baggrund</option>
            <option value="video_bg" :selected="hero?.type === 'video_bg'">Video-baggrund</option>
            <option value="split" :selected="hero?.type === 'split'">Tekst + billede</option>
        </select>
    </div>

    <template x-if="hero">
        <div class="space-y-3 p-4">

            {{-- media --}}
            <div>
                <template x-if="hero.media">
                    <div class="flex items-center gap-3">
                        <template x-if="hero.type === 'video_bg'">
                            <video :src="hero.media.url" class="h-16 w-24 rounded-lg border border-slate-200 object-cover" muted></video>
                        </template>
                        <template x-if="hero.type !== 'video_bg'">
                            <img :src="hero.media.url" class="h-16 w-24 rounded-lg border border-slate-200 object-cover">
                        </template>
                        <button type="button" @click="pickMedia({type: hero.type === 'video_bg' ? 'video' : 'image'}, m => hero.media = m)" class="text-sm text-brand-700 hover:underline">Skift</button>
                        <button type="button" @click="hero.media = null" class="text-sm text-rose-600 hover:underline">Fjern</button>
                    </div>
                </template>
                <template x-if="!hero.media">
                    <button type="button" @click="pickMedia({type: hero.type === 'video_bg' ? 'video' : 'image'}, m => hero.media = m)"
                            class="flex w-full items-center justify-center rounded-lg border-2 border-dashed border-slate-300 py-6 text-sm text-slate-500 hover:border-brand hover:text-brand-700"
                            x-text="hero.type === 'video_bg' ? 'Vælg baggrundsvideo' : 'Vælg baggrundsbillede'"></button>
                </template>
            </div>

            {{-- headline --}}
            <input x-model="hero.headline" placeholder="Overskrift"
                   class="w-full rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">

            {{-- subhead (bg types) --}}
            <template x-if="hero.type !== 'split'">
                <textarea x-model="hero.subhead" rows="2" placeholder="Underoverskrift"
                          class="w-full rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand"></textarea>
            </template>

            {{-- body (split) --}}
            <template x-if="hero.type === 'split'">
                <div contenteditable x-init="$el.innerHTML = hero.body || ''" @input="hero.body = $event.target.innerHTML"
                     class="prose prose-sm max-w-none min-h-[4rem] rounded-lg border border-slate-200 p-2.5 focus:border-brand focus:outline-none"></div>
            </template>

            {{-- layout option --}}
            <template x-if="hero.type === 'image_bg'">
                <label class="flex items-center gap-2 text-sm text-slate-600">Justering
                    <select x-model="hero.align" class="rounded-lg border-slate-200 py-1 text-sm">
                        <option value="left">Venstre</option>
                        <option value="center">Centreret</option>
                    </select>
                </label>
            </template>
            <template x-if="hero.type === 'split'">
                <label class="flex items-center gap-2 text-sm text-slate-600">Billedet til
                    <select x-model="hero.side" class="rounded-lg border-slate-200 py-1 text-sm">
                        <option value="right">Højre</option>
                        <option value="left">Venstre</option>
                    </select>
                </label>
            </template>

            {{-- button --}}
            <div class="flex gap-2">
                <input x-model="hero.button_label" placeholder="Knaptekst (valgfri)" class="w-1/2 rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
                <input x-model="hero.button_href" placeholder="Knap-URL" class="w-1/2 rounded-lg border-slate-200 text-sm focus:border-brand focus:ring-brand">
            </div>
        </div>
    </template>
</section>
