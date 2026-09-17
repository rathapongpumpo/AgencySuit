<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleOAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/today');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
    Route::get('/auth/google', [GoogleOAuthController::class, 'redirect'])->middleware('throttle:6,1')->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleOAuthController::class, 'callback'])->middleware('throttle:6,1')->name('google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::view('/today', 'today')->name('today');
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::post('/properties/{property}/photos', [PropertyController::class, 'storePhoto'])->name('properties.photos.store');
    Route::patch('/properties/{property}/photos/{photo}/primary', [PropertyController::class, 'setPrimaryPhoto'])->name('properties.photos.primary');
    Route::delete('/properties/{property}/photos/{photo}', [PropertyController::class, 'destroyPhoto'])->name('properties.photos.destroy');
    Route::get('/properties/{property}/photos/{photo}/file', [PropertyController::class, 'showPhoto'])->name('properties.photos.file');
    Route::get('/properties/{property}/photos/{photo}/thumbnail', [PropertyController::class, 'showPhotoThumbnail'])->name('properties.photos.thumbnail');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::patch('/properties/{property}/status', [PropertyController::class, 'updateStatus'])->name('properties.status.update');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::view('/more', 'more')->name('more');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
