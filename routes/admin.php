<?php

use Illuminate\Support\Facades\Route;
use Webkraft\Cms\Http\Controllers\Admin\DashboardController;
use Webkraft\Cms\Http\Controllers\Admin\MediaController;
use Webkraft\Cms\Http\Controllers\Admin\PageController;
use Webkraft\Cms\Http\Controllers\Admin\SettingsController;

/*
| Webkraft admin routes. Prefix + middleware are applied by the service
| provider, so paths here are relative to config('webkraft.path').
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Pages
Route::get('/pages',                  [PageController::class, 'index'])->name('pages.index');
Route::post('/pages/reorder',         [PageController::class, 'reorder'])->name('pages.reorder');
Route::get('/pages/create',           [PageController::class, 'create'])->name('pages.create');
Route::post('/pages',                 [PageController::class, 'store'])->name('pages.store');
Route::get('/pages/{page}/edit',      [PageController::class, 'edit'])->name('pages.edit');
Route::patch('/pages/{page}',         [PageController::class, 'update'])->name('pages.update');
Route::post('/pages/{page}/publish',  [PageController::class, 'togglePublish'])->name('pages.publish');
Route::delete('/pages/{page}',        [PageController::class, 'destroy'])->name('pages.destroy');

// Media archive
Route::get('/media',           [MediaController::class, 'index'])->name('media.index');
Route::get('/media/list',      [MediaController::class, 'list'])->name('media.list');
Route::post('/media',          [MediaController::class, 'store'])->name('media.store');
Route::patch('/media/{media}', [MediaController::class, 'update'])->name('media.update');
Route::delete('/media/{media}',[MediaController::class, 'destroy'])->name('media.destroy');

// Settings
Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');
