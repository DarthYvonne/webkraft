<?php

namespace Webkraft\Cms\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Webkraft\Cms\Models\Media;
use Webkraft\Cms\Models\Page;

class DashboardController extends Controller
{
    public function index()
    {
        return view('webkraft::dashboard', [
            'pageCount'      => Page::count(),
            'publishedCount' => Page::published()->count(),
            'mediaCount'     => Media::count(),
            'recent'         => Page::ordered()->latest('updated_at')->limit(6)->get(),
        ]);
    }
}
