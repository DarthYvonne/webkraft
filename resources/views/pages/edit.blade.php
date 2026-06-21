@extends('webkraft::layouts.admin')

@php
    $base    = $webkraftBase;
    $isNew   = ! $page->exists;
    $action  = $isNew ? "{$base}/pages" : "{$base}/pages/{$page->id}";
@endphp

@section('title', $isNew ? 'Ny side' : $page->title)

@section('content')
<form method="POST" action="{{ $action }}"
      x-data="webkraftPageEditor({
          isNew: {{ $isNew ? 'true' : 'false' }},
          title: @js($page->title),
          slug:  @js($page->slug),
          isPublished: {{ $page->is_published ? 'true' : 'false' }},
          hero:  @js($page->hero),
          body:  @js($page->body ?? [])
      })">
    @csrf
    @unless ($isNew) @method('PATCH') @endunless

    {{-- serialized editor state --}}
    <input type="hidden" name="hero" :value="hero ? JSON.stringify(hero) : ''">
    <input type="hidden" name="body" :value="JSON.stringify(body)">
    <input type="hidden" name="slug" :value="slug">
    <input type="hidden" name="is_published" :value="isPublished ? 1 : 0">

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        {{-- Main column --}}
        <div class="space-y-6">
            <input x-model="title" name="title" @input="onTitle()" required
                   placeholder="Sidens titel"
                   class="w-full border-0 bg-transparent p-0 text-3xl font-bold tracking-tight text-slate-900 placeholder:text-slate-300 focus:ring-0">

            @include('webkraft::pages.partials.hero-editor')
            @include('webkraft::pages.partials.body-editor')
        </div>

        {{-- Sidebar column --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">Status</span>
                    <button type="button" @click="isPublished = !isPublished"
                            :class="isPublished ? 'bg-emerald-500' : 'bg-slate-300'"
                            class="relative h-6 w-11 rounded-full transition">
                        <span :class="isPublished ? 'translate-x-5' : 'translate-x-0.5'"
                              class="absolute top-0.5 h-5 w-5 rounded-full bg-white transition"></span>
                    </button>
                </div>
                <p class="text-xs text-slate-400" x-text="isPublished ? 'Siden er synlig på websitet.' : 'Kladde — kun synlig her.'"></p>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">URL</label>
                    <div class="flex items-center rounded-lg border border-slate-300 px-2 focus-within:border-brand focus-within:ring-1 focus-within:ring-brand">
                        <span class="text-xs text-slate-400">/</span>
                        <input x-model="slug" @input="slugTouched = true" type="text"
                               class="w-full border-0 p-1.5 text-sm focus:ring-0" placeholder="url-stump">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Overside</label>
                    <select name="parent_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
                        <option value="">— Topniveau —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected($page->parent_id === $parent->id)>{{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <details class="rounded-xl border border-slate-200 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium">SEO</summary>
                <div class="mt-3 space-y-3">
                    <input name="seo_title" value="{{ $page->seo_title }}" placeholder="SEO-titel"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">
                    <textarea name="seo_description" rows="3" placeholder="Meta-beskrivelse"
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-brand focus:ring-brand">{{ $page->seo_description }}</textarea>
                </div>
            </details>

            <div class="flex flex-col gap-2">
                <button class="rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Gem</button>
                <button name="save_and_view" value="1" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Gem og vis</button>
                @unless ($isNew)
                    <a href="{{ $base }}/pages" class="rounded-lg px-4 py-2.5 text-center text-sm text-slate-400 hover:text-slate-600">Tilbage</a>
                @endunless
            </div>

            @unless ($isNew)
                <form method="POST" action="{{ $base }}/pages/{{ $page->id }}" onsubmit="return confirm('Slet denne side?')">
                    @csrf @method('DELETE')
                    <button class="w-full rounded-lg px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">Slet siden</button>
                </form>
            @endunless
        </div>
    </div>
</form>
@endsection
