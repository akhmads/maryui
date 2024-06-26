<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Livewire\Forms\CountryForm;
use App\Models\Country;

new class extends Component {
    use Toast, WithPagination;

    public CountryForm $form;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public bool $formModal = false;
    public ?string $formTitle;

    // Clear filters
    public function clear(): void
    {
        $this->warning('Filters cleared', position: 'toast-bottom');
        $this->reset();
        $this->resetPage();
    }

    // Delete action
    public function delete(Country $country): void
    {
        $country->delete();
        $this->warning("$country->name deleted", 'Good bye!');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name'],
        ];
    }

    public function countries(): LengthAwarePaginator
    {
        return Country::query()
        ->orderBy(...array_values($this->sortBy))
        ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
        ->paginate(8);
    }

    public function with(): array
    {
        return [
            'countries' => $this->countries(),
            'headers' => $this->headers(),
        ];
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            $this->resetPage();
        }
    }

    public function openModal(): void
    {
        $this->form->init();
        $this->formModal = true;
    }

    public function create(): void
    {
        $this->openModal();
        $this->formTitle = 'Create';
    }

    public function edit(Country $country): void
    {
        $this->openModal();
        $this->formTitle = 'Update';
        $this->form->set($country);
    }

    public function save(): void
    {
        $this->validate();
        $this->form->save();
        $this->formModal = false;
        $this->success('Country has been saved.');
    }
}; ?>

<div>
    <!-- HEADER -->
    {{-- class="sticky top-6 z-10 bg-gray-50" --}}
    <x-header title="Countries" subtitle="Master Countries" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            {{-- <x-button label="Create" link="/countries/create" responsive icon="o-plus" class="btn-primary" /> --}}
            <x-button label="Create" wire:click="create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$countries" :sort-by="$sortBy" with-pagination @row-click="$wire.edit($event.detail.id)"> {{-- link="countries/{id}/edit" --}}
            @scope('actions', $country)
            <x-button icon="o-trash" wire:click="delete({{ $country['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    <!-- FORM MODAL -->
    <x-modal wire:model="formModal" title="{{ $formTitle }}">
        <x-form wire:submit="save">
            <x-input label="Country Name" wire:model="form.name" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.formModal = false" />
                <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
