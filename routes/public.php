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

// Fallback: only handles URLs the host app hasn't already claimed, so it can
// never shadow the host's own routes regardless of provider load order.
Route::fallback([PageController::class, 'show'])->name('webkraft.page');
