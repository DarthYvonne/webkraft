<?php

namespace Webkraft\Cms\Http\Controllers;

use Illuminate\Routing\Controller;
use Webkraft\Cms\Models\Page;
use Webkraft\Cms\Support\Nav;

class PageController extends Controller
{
    /** Render a published page by its (possibly nested) slug path. */
    public function show(string $path)
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), fn ($s) => $s !== ''));

        if (count($segments) === 1) {
            $page = Page::topLevel()->where('slug', $segments[0])->published()->firstOrFail();
        } elseif (count($segments) === 2) {
            $parent = Page::topLevel()->where('slug', $segments[0])->published()->firstOrFail();
            $page = Page::where('parent_id', $parent->id)->where('slug', $segments[1])->published()->firstOrFail();
            $page->setRelation('parent', $parent);
        } else {
            abort(404);
        }

        return view('webkraft::page', [
            'page'    => $page,
            'menu'    => Nav::menu(),
            'sidebar' => Nav::sidebarFor($page),
        ]);
    }
}
