@php $root = $sidebar['root']; $children = $sidebar['children']; @endphp
<aside>
    <nav class="space-y-1 text-sm lg:sticky lg:top-20">
        <a href="{{ $root->url() }}"
           class="block px-2 py-1.5 font-semibold {{ $current->id === $root->id ? 'wk-accent' : 'text-slate-900' }}">
            {{ $root->title }}
        </a>
        @foreach ($children as $child)
            <a href="{{ $child->url() }}"
               class="block rounded-lg px-2 py-1.5 {{ $current->id === $child->id ? 'wk-accent-bg text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                {{ $child->title }}
            </a>
        @endforeach
    </nav>
</aside>
