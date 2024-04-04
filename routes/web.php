<?php

use Livewire\Volt\Volt;

Volt::route('/', 'home');
Volt::route('/users', 'users.index');
Volt::route('/users/create', 'users.create');
Volt::route('/users/{user}/edit', 'users.edit');
