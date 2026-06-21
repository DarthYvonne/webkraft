<?php

namespace Webkraft\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkraft\Cms\Models\Page;
use Webkraft\Cms\Models\Setting;
use Webkraft\Cms\Support\Nav;

class PageController extends Controller
{
    /** Render a published page by its (possibly nested) slug path. */
    public function show(Request $request)
    {
        $path = trim($request->path(), '/');
        $segments = $path === '' ? [] : array_values(array_filter(explode('/', $path), fn ($s) => $s !== ''));

        if ($segments === []) {
            // Root — render the configured home page, or let the host 404.
            $homeId = Setting::get('home_page_id');
            abort_unless($homeId, 404);
            $page = Page::published()->findOrFail((int) $homeId);
        } elseif (count($segments) === 1) {
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
