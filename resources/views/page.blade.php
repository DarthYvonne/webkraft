@extends('webkraft::layouts.public')

@section('title', $page->seo_title ?: $page->title)
@if ($page->seo_description)
    @section('meta_description', $page->seo_description)
@endif

@section('body')
    @include('webkraft::render.hero', ['hero' => $page->hero, 'page' => $page])

    <div class="wk-container mx-auto px-6 py-12">
        @if ($sidebar->isNotEmpty())
            <div class="grid gap-10 lg:grid-cols-[220px_1fr]">
                @include('webkraft::partials.public-sidebar', ['sidebar' => $sidebar, 'current' => $page])
                <article class="prose max-w-none">
                    @include('webkraft::render.body', ['blocks' => $page->body ?? []])
                </article>
            </div>
        @else
            <article class="prose mx-auto max-w-3xl">
                @include('webkraft::render.body', ['blocks' => $page->body ?? []])
            </article>
        @endif
    </div>
@endsection
