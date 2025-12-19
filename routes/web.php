<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductLandingController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\ReceiverController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Application;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products resource routes
    Route::resource('products', ProductController::class);
    Route::get('api/products/{id}', [ProductController::class, 'show'])->name('api.products.show');

    // Categories routes
    Route::resource('categories', CategoryController::class);
    Route::get('api/categories/{id}', [CategoryController::class, 'show'])->name('api.categories.show');

    // sources routes
    Route::resource('sources', SourceController::class);
    Route::get('api/sources/{id}', [SourceController::class, 'show'])->name('api.sources.show');

    // receivers routes
    Route::resource('receivers', ReceiverController::class);
    Route::get('api/receivers/{id}', [ReceiverController::class, 'show'])->name('api.receivers.show');

});
    Route::get('api/products-landing-search', [ProductLandingController::class, 'search'])->name('api.products.search');
    Route::get('api/products-categories-search', [ProductLandingController::class, 'categories'])->name('api.products.categories');


require __DIR__.'/auth.php';
