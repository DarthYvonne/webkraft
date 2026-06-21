<?php

use Illuminate\Support\Facades\Route;
use Webkraft\Cms\Http\Controllers\Admin\DashboardController;

/*
| Webkraft admin routes. Prefix + middleware are applied by the service
| provider, so paths here are relative to config('webkraft.path').
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
