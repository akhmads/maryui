<?php

use Livewire\Volt\Component;

new class extends Component {

    public ?string $date1;
    public ?string $date2;
    public ?string $show = '';

    public function mount(): void
    {
        $this->date1 = date('Y-m-01');
        $this->date2 = date('Y-m-t');
    }

    public function filter(): void
    {
        $this->show = $this->date1 . ' - ' . $this->date2;
    }
}; ?>
<div>
    @php
    $configDate = [
        'altInput' => true,
        'altFormat' => 'F j, Y',
        'dateFormat' => 'Y-m-d',
    ];
    @endphp
    <x-header title="Dashboard" subtitle="Application Dashboard" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            {{-- <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" /> --}}
            <div class="flex items-center gap-3">
                <x-datepicker label="" wire:model="date1" icon-right="o-calendar" :config="$configDate" />
                <x-datepicker label="" wire:model="date2" icon-right="o-calendar" :config="$configDate" />
            </div>
        </x-slot:middle>
        <x-slot:actions>
            {{-- <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" /> --}}
            <x-button label="Filter" wire:click="filter" spinner="filter" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-5">
        <div class="bg-base-50 grid lg:grid-cols-4 gap-5">
            <x-stat title="Messages" value="44" icon="o-envelope" tooltip="Hello" class="shadow-xs border border-slate-200" />
            <x-stat title="Sales" value="22.124" icon="o-arrow-trending-up" tooltip="There" class="shadow-xs border border-slate-200" />
            <x-stat title="Users" value="23" icon="o-user" tooltip-bottom="Users" class="shadow-xs border border-slate-200" />
            <x-stat title="Other" value="1000" icon="o-paper-airplane" tooltip-bottom="Other" class="shadow-xs border border-slate-200" />
        </div>

        <div class="lg:grid grid-cols-12 gap-5">
            <x-card title="Bar Chart" class="col-span-8 flex justify-items-center" shadow separator>
                <livewire:dashboard.bar-chart lazy />
            </x-card>
            <x-card title="Pie Chart" class="col-span-4" shadow separator>
                <livewire:dashboard.pie-chart lazy />
            </x-card>
        </div>

        <x-button label="Search" @click.stop="$dispatch('mary-search-open')" />
    </div>
</div>
