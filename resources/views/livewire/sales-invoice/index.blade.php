<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Models\Contact;
use App\Models\SalesInvoice;

new class extends Component {
    use Toast, WithPagination;

    public ?string $search = '';
    public ?int $contact_id = null;
    public bool $drawer = false;
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];
    public Collection $contactSearchable;

    public function clear(): void
    {
        $this->warning('Filters cleared', position: 'toast-bottom');
        $this->reset();
        $this->resetPage();
        $this->searchContact();
    }

    public function delete(SalesInvoice $salesInvoice): void
    {
        $salesInvoice->details()->delete();
        $salesInvoice->delete();
        $this->warning("Sales invoice has been deleted", 'Good bye!', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'date', 'label' => 'Date'],
            ['key' => 'code', 'label' => 'Code'],
            ['key' => 'contact_name', 'label' => 'Customer'],
            ['key' => 'qty', 'label' => 'Qty', 'class' => 'text-right w-[80px]'],
            ['key' => 'total', 'label' => 'Total', 'class' => 'text-right w-[150px]'],
        ];
    }

    public function salesInvoice(): LengthAwarePaginator
    {
        return SalesInvoice::query()
        ->withAggregate('contact', 'name')
        ->orderBy(...array_values($this->sortBy))
        ->when($this->search, fn(Builder $q) => $q->where('code', 'like', "%$this->search%"))
        ->when($this->contact_id, fn(Builder $q) => $q->where('contact_id', $this->contact_id))
        ->paginate(8);
    }

    public function searchContact(string $value = ''): void
    {
        $selectedOption = Contact::where('id', $this->contact_id)->get();
        $this->contactSearchable = Contact::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption);
    }

    public function mount(): void
    {
        $this->searchContact();
    }

    public function with(): array
    {
        return [
            'salesInvoice' => $this->salesInvoice(),
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Sales Invoice" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button label="Create" link="/sales-invoice/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$salesInvoice" :sort-by="$sortBy" with-pagination link="sales-invoice/{id}/edit">
            @scope('cell_qty', $sales)
            <div class="text-right">{{ $sales->qty }}</div>
            @endscope
            @scope('cell_total', $sales)
            <div class="text-right">{{ $sales->total }}</div>
            @endscope
            @scope('actions', $sales)
            <x-button icon="o-trash" wire:click="delete({{ $sales['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-choices label="Customer" wire:model.live="contact_id" :options="$contactSearchable" search-function="searchContact" single searchable />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
