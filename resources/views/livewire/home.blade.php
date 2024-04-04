<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <h2 class="mb-10">Welcome</h2>

    <div class="pb-5 bg-base-50 grid md:grid-cols-4 gap-5">
        <x-stat title="Messages" value="44" icon="o-envelope" tooltip="Hello" lazy />
        <x-stat title="Sales" description="This month" value="22.124" icon="o-arrow-trending-up" tooltip-bottom="There" />
    </div>

    <x-button label="Search" @click.stop="$dispatch('mary-search-open')" />
</div>
