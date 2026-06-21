<?php

use Illuminate\Support\Facades\Route;
use Webkraft\Cms\Http\Controllers\ContactController;
use Webkraft\Cms\Http\Controllers\PageController;

/*
| Public page rendering. Catch-all by slug path (one or two levels), so
| published Webkraft pages render on the host site. Registered last and
| only matches what the host hasn't already claimed. Disable via
| config('webkraft.public_routes') if the host renders pages itself.
*/

Route::post('/wk/contact', [ContactController::class, 'submit'])->name('webkraft.contact');

Route::get('/{path}', [PageController::class, 'show'])
    ->where('path', '[A-Za-z0-9\-_/]+')
    ->name('webkraft.page');
