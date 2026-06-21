@extends('webkraft::layouts.admin')

@section('title', 'Indstillinger')

@section('content')
<form method="POST" action="{{ $webkraftBase }}/settings"
      x-data="{ logo: @js($s['brand_logo']), primary: @js($s['brand_primary'] ?: '#4f46e5') }"
      class="max-w-2xl space-y-6">
    @csrf

    {{-- Branding --}}
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-5 py-3 font-semibold">Brand</div>
        <div class="space-y-5 p-5">

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Sitenavn</label>
                <input name="brand_name" value="{{ $s['brand_name'] }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Logo</label>
                <input type="hidden" name="brand_logo" :value="logo || ''">
                <div class="flex items-center gap-4">
                    <div class="grid h-16 w-32 place-items-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                        <template x-if="logo"><img :src="logo" class="max-h-full max-w-full object-contain"></template>
                        <template x-if="!logo"><span class="text-xs text-slate-400">Intet logo</span></template>
                    </div>
                    <button type="button" @click="$store.mediaPicker.show('{{ $webkraftBase }}', {type:'image'}, m => logo = m.url)"
                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium hover:bg-slate-50">Vælg logo</button>
                    <button type="button" x-show="logo" @click="logo = null" class="text-sm text-rose-600 hover:underline">Fjern</button>
                </div>
                <p class="mt-1 text-xs text-slate-400">Vises i topmenuen. Uden logo vises sitenavnet som tekst.</p>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Accentfarve</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="primary" class="h-9 w-12 rounded border-slate-300">
                        <input name="brand_primary" x-model="primary" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Indholdsbredde</label>
                    <input name="brand_container" value="{{ $s['brand_container'] }}" placeholder="72rem"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
                </div>
            </div>
        </div>
    </div>

    {{-- Contact --}}
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-5 py-3 font-semibold">Kontakt</div>
        <div class="p-5">
            <label class="mb-1 block text-sm font-medium text-slate-700">Modtager af kontaktformularer</label>
            <input name="contact_email" type="email" value="{{ $s['contact_email'] }}" placeholder="dig@firma.dk"
                   class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
        </div>
    </div>

    <button class="rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Gem indstillinger</button>
</form>
@endsection
