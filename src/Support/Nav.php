<?php

namespace Webkraft\Cms\Support;

use Illuminate\Support\Collection;
use Webkraft\Cms\Models\Page;

class Nav
{
    /** Top navigation: published top-level pages with their published children (dropdowns). */
    public static function menu(): Collection
    {
        return Page::query()
            ->topLevel()
            ->published()
            ->with(['children' => fn ($q) => $q->published()])
            ->ordered()
            ->get();
    }

    /**
     * Sidebar for a page: the section root (top-level ancestor) plus its
     * published children. Empty when the section has no sub-pages.
     */
    public static function sidebarFor(Page $page): Collection
    {
        $root = $page->parent_id ? $page->parent : $page;

        if (! $root) {
            return collect();
        }

        $children = $root->children()->published()->get();

        return $children->isEmpty() ? collect() : collect(['root' => $root, 'children' => $children]);
    }
}
