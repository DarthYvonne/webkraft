<?php

namespace Webkraft\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkraft\Cms\Models\Page;
use Webkraft\Cms\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        return view('webkraft::settings', [
            's' => [
                'brand_name'      => Setting::get('brand_name', config('webkraft.brand.name')),
                'brand_logo'      => Setting::get('brand_logo'),
                'brand_primary'   => Setting::get('brand_primary', config('webkraft.brand.primary', '#4f46e5')),
                'brand_container' => Setting::get('brand_container', config('webkraft.brand.container', '72rem')),
                'contact_email'   => Setting::get('contact_email', config('webkraft.contact_email')),
                'home_page_id'    => Setting::get('home_page_id'),
            ],
            'pages' => Page::topLevel()->ordered()->get(),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'brand_name'      => 'nullable|string|max:120',
            'brand_logo'      => 'nullable|string|max:1000',
            'brand_primary'   => 'nullable|string|max:32',
            'brand_container' => 'nullable|string|max:32',
            'contact_email'   => 'nullable|email|max:255',
            'home_page_id'    => 'nullable|integer|exists:webkraft_pages,id',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('status', 'Indstillinger gemt.');
    }
}
