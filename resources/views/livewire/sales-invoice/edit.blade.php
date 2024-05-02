<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Models\SalesInvoice;
use App\Models\Contact;

new class extends Component {
    use Toast;

    public SalesInvoice $salesInvoice;

    #[Rule('required')]
    public string $code = '';

    #[Rule('required')]
    public string $date = '';

    #[Rule('required')]
    public ?int $contact_id = null;

    #[Rule('sometimes')]
    public array $details = [];

    public Collection $contactSearchable;

    public function mount(): void
    {
        $this->searchContact();
        $this->fill($this->salesInvoice);
    }

    public function with(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['details']);

        $this->salesInvoice->update($data);

        $this->success('Invoice created with success.', redirectTo: '/sales-invoice');
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
}; ?>

<div>
    <x-header title="Create New Sales Invoice" separator />
    <x-form wire:submit="save">
        <div class="grid grid-cols-12 gap-6">
            <x-card class="col-span-8">
                <div class="space-y-4">
                    <x-input label="Code" wire:model="code" />
                </div>
            </x-card>
            <x-card class="col-span-4">
                <div class="space-y-4">
                    <x-datetime label="Date" wire:model="date" />
                    <x-choices label="Contact" wire:model="contact_id" :options="$contactSearchable" search-function="searchContact" single searchable />
                </div>
            </x-card>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" link="/sales-invoice" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
