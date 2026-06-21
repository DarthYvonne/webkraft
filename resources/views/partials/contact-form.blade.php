{{-- Contact form block. Posts to the package contact handler. --}}
<div class="not-prose my-8 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8">
    <h3 class="text-xl font-bold text-slate-900">{{ $heading ?? 'Kontakt os' }}</h3>

    @if (session('wk_contact_sent'))
        <p class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Tak! Vi vender tilbage hurtigst muligt.</p>
    @else
        <form method="POST" action="{{ route('webkraft.contact') }}" class="mt-4 space-y-3">
            @csrf
            {{-- honeypot --}}
            <input type="text" name="company" tabindex="-1" autocomplete="off" class="hidden">
            <div class="grid gap-3 sm:grid-cols-2">
                <input name="name" required placeholder="Navn" value="{{ old('name') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-[color:var(--wk-primary)] focus:ring-[color:var(--wk-primary)]">
                <input name="email" type="email" required placeholder="E-mail" value="{{ old('email') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-[color:var(--wk-primary)] focus:ring-[color:var(--wk-primary)]">
            </div>
            <textarea name="message" rows="4" required placeholder="Besked"
                      class="w-full rounded-lg border-slate-300 text-sm focus:border-[color:var(--wk-primary)] focus:ring-[color:var(--wk-primary)]">{{ old('message') }}</textarea>
            @error('email')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            <button class="wk-accent-bg rounded-lg px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">{{ $button ?? 'Send' }}</button>
        </form>
    @endif
</div>
