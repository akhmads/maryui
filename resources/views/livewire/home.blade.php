<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <h2>Welcome</h2>

    <x-button label="Search" @click.stop="$dispatch('mary-search-open')" />
</div>
