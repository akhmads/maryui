<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Livewire\Forms\ItemForm;
use App\Models\Item;

new class extends Component {
    use Toast, WithPagination;

    public ItemForm $form;

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
    public function delete(Item $item): void
    {
        $item->delete();
        $this->warning("$item->name deleted", 'Good bye!');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'price', 'label' => 'Price'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }

    public function items(): LengthAwarePaginator
    {
        return Item::query()
        ->orderBy(...array_values($this->sortBy))
        ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
        ->paginate(8);
    }

    public function with(): array
    {
        return [
            'items' => $this->items(),
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

    public function edit(Item $item): void
    {
        $this->openModal();
        $this->formTitle = 'Update';
        $this->form->set($item);
    }

    public function save(): void
    {
        $this->form->beforeValidation();
        $this->validate();
        $this->form->save();
        $this->formModal = false;
        $this->success('Item has been saved.');
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Items" subtitle="Master Items" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Create" wire:click="create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$items" :sort-by="$sortBy" with-pagination @row-click="$wire.edit($event.detail.id)">
            @scope('cell_price', $item)
                {{ \Illuminate\Support\Number::format($item->price, precision: 2) }}
            @endscope
            @scope('cell_status', $item)
                <x-badge :value="$item->status->value" class="{{ $item->status->color() }}" />
            @endscope
            @scope('actions', $item)
            <x-button icon="o-trash" wire:click="delete({{ $item['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- FORM MODAL -->
    <x-modal wire:model="formModal" title="{{ $formTitle }}">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="form.name" />
            <x-input label="Price" wire:model="form.price" x-mask:dynamic="$money($input, '.', ',')" />
            <x-select label="Status" :options="\App\Enums\ActiveStatus::toSelect()" wire:model="form.status" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.formModal = false" />
                <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
