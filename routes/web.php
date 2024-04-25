<?php

use Livewire\Volt\Volt;

Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register');

Route::middleware('auth')->group(function () {
    Volt::route('/', 'home')->lazy();
    Volt::route('/users', 'users.index');
    Volt::route('/users/create', 'users.create');
    Volt::route('/users/{user}/edit', 'users.edit');
    Volt::route('/posts', 'posts.index');
    Volt::route('/posts/create', 'posts.create');
    Volt::route('/posts/{post}/edit', 'posts.edit');
    Volt::route('/countries', 'countries.index');
    Volt::route('/contacts', 'contacts.index');
    Volt::route('/items', 'items.index');
});

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});
