@php $base = $webkraftBase; @endphp
<li data-id="{{ $page->id }}" class="rounded-xl border border-slate-200 bg-white">
    <div class="flex items-center gap-3 px-4 py-3">
        <button data-handle class="cursor-grab text-slate-300 hover:text-slate-500" title="Træk for at omarrangere">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 11-4 0 2 2 0 014 0zM8 12a2 2 0 11-4 0 2 2 0 014 0zM6 20a2 2 0 100-4 2 2 0 000 4zM20 6a2 2 0 11-4 0 2 2 0 014 0zM18 14a2 2 0 100-4 2 2 0 000 4zM20 18a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </button>

        <a href="{{ $base }}/pages/{{ $page->id }}/edit" class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <span class="truncate font-medium">{{ $page->title }}</span>
                <span class="rounded-full px-2 py-0.5 text-xs {{ $page->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $page->is_published ? 'Publiceret' : 'Kladde' }}
                </span>
            </div>
            <div class="truncate text-xs text-slate-400">{{ $page->url() }}</div>
        </a>

        <form method="POST" action="{{ $base }}/pages/{{ $page->id }}/publish" class="shrink-0">
            @csrf
            <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-100" title="Skift status">
                {{ $page->is_published ? 'Afpublicer' : 'Publicer' }}
            </button>
        </form>

        @unless ($page->parent_id)
            <a href="{{ $base }}/pages/create?parent={{ $page->id }}"
               class="shrink-0 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-700 hover:bg-brand/10">+ Underside</a>
        @endunless

        <a href="{{ $base }}/pages/{{ $page->id }}/edit" class="shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Rediger">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </a>
    </div>

    @unless ($page->parent_id)
        <ul data-sortable data-parent="{{ $page->id }}" class="space-y-2 px-4 pb-3 pl-12 {{ $page->children->isEmpty() ? 'min-h-[0.5rem]' : '' }}">
            @foreach ($page->children as $child)
                <li data-id="{{ $child->id }}" class="flex items-center gap-3 rounded-lg border border-slate-100 bg-slate-50/60 px-3 py-2">
                    <button data-handle class="cursor-grab text-slate-300 hover:text-slate-500">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 11-4 0 2 2 0 014 0zM8 12a2 2 0 11-4 0 2 2 0 014 0zM6 20a2 2 0 100-4 2 2 0 000 4zM20 6a2 2 0 11-4 0 2 2 0 014 0zM18 14a2 2 0 100-4 2 2 0 000 4zM20 18a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </button>
                    <a href="{{ $base }}/pages/{{ $child->id }}/edit" class="min-w-0 flex-1 truncate text-sm">{{ $child->title }}</a>
                    <span class="rounded-full px-2 py-0.5 text-xs {{ $child->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $child->is_published ? 'Publiceret' : 'Kladde' }}
                    </span>
                </li>
            @endforeach
        </ul>
    @endunless
</li>
