<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register');

Route::middleware('auth')->group(function () {
    Volt::route('/', 'home');
    Volt::route('/home2', 'home2');
    Volt::route('/users', 'users.index');
    Volt::route('/users/create', 'users.create');
    Volt::route('/users/{user}/edit', 'users.edit');
    Volt::route('/posts', 'posts.index');
    Volt::route('/posts/create', 'posts.create');
    Volt::route('/posts/{post}/edit', 'posts.edit');
    Volt::route('/countries', 'countries.index');
    Volt::route('/contacts', 'contacts.index');
    Volt::route('/contacts/import', 'contacts.import');
    Volt::route('/items', 'items.index');

    Volt::route('/sales-invoice', 'sales-invoice.index');
    Volt::route('/sales-invoice/create', 'sales-invoice.create');
    Volt::route('/sales-invoice/{salesInvoice}/edit', 'sales-invoice.edit');

    Route::get('/contacts/export', [\App\Http\Controllers\ContactController::class, 'export']);
});

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});
