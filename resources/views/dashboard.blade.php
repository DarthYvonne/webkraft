@extends('webkraft::layouts.admin')

@section('title', 'Oversigt')

@php $base = '/'.trim(config('webkraft.path', 'cms'), '/'); @endphp

@section('actions')
    <a href="{{ $base }}/pages/create"
       class="inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Ny side
    </a>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['Sider', $pageCount, $base.'/pages'],
            ['Publicerede', $publishedCount, $base.'/pages'],
            ['Medier', $mediaCount, $base.'/media'],
        ] as [$label, $value, $href])
            <a href="{{ $href }}" class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-brand/40 hover:shadow-sm">
                <div class="text-sm text-slate-500">{{ $label }}</div>
                <div class="mt-1 text-3xl font-semibold tracking-tight">{{ $value }}</div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-semibold">Senest redigeret</h2>
            <a href="{{ $base }}/pages" class="text-sm text-brand-700 hover:underline">Alle sider →</a>
        </div>
        @if ($recent->isEmpty())
            <div class="px-5 py-10 text-center text-sm text-slate-500">
                Ingen sider endnu. <a href="{{ $base }}/pages/create" class="text-brand-700 hover:underline">Opret den første</a>.
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($recent as $page)
                    <li>
                        <a href="{{ $base }}/pages/{{ $page->id }}/edit" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50">
                            <span class="font-medium">{{ $page->title }}</span>
                            <span class="flex items-center gap-3 text-xs">
                                <span class="rounded-full px-2 py-0.5 {{ $page->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $page->is_published ? 'Publiceret' : 'Kladde' }}
                                </span>
                                <span class="text-slate-400">{{ $page->updated_at?->diffForHumans() }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
