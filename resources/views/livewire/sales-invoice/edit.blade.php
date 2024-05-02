<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Models\SalesInvoice;
use App\Models\Contact;
use App\Models\Item;

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
    public Collection $details;

    public Collection $contactSearchable;
    public Collection $itemSearchable;

    public function mount(): void
    {
        $this->fill($this->salesInvoice);
        $this->details = collect([]);
        $this->searchContact();
        $this->searchItem();
    }

    public function headers(): array
    {
        return [
            ['key' => 'item_id', 'label' => 'ID'],
            ['key' => 'item_name', 'label' => 'Name'],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['details']);

        $this->salesInvoice->update($data);

        $this->saveDetail();

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

    public function searchItem(string $value = ''): void
    {
        $this->itemSearchable = Item::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get();
    }

    public function addDetail()
    {
        $this->details->push([
            'item_id' => '1',
            'qty' => '1'
        ]);
    }

    public function deleteDetail($key)
    {
        $this->details->forget($key);
    }

    public function saveDetail()
    {
        foreach ($this->details->all() as $detail)
        {
            $this->salesInvoice->details()->create([
                'sales_invoice_id' => $this->salesInvoice->id,
                'item_id' => $detail['item_id'],
                'qty' => $detail['qty'],
                'price' => 0,
                'subtotal' => 0,
            ]);
        }
    }
}; ?>

<div>
    <x-header title="Create New Sales Invoice" separator />
    <x-form wire:submit="save">
        <div class="grid grid-cols-12 gap-6">
            <x-card class="col-span-8">
                <div class="space-y-4">
                    <x-input label="Code" wire:model="code" />

                    <!-- TABLE DETAIL -->
                    <x-button wire:click="addDetail" label="Add Detail" icon="o-plus" spinner="addDetail" type="button" class="btn-primary" />
                    @dump($details)
                    <table class="table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th class="w-56">Qty</th>
                        <th class="w-36">#</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse ( $details->all() as $key => $detail )
                    <tr wire:key="item-detail-{{ $key }}">
                        <td><x-choices label="" wire:model.live="details.{{$key}}.item_id" :options="$itemSearchable" search-function="searchItem" single searchable /></td>
                        <td><x-input label="" wire:model.live.debounce="details.{{$key}}.qty" /></td>
                        <td><x-button wire:click="deleteDetail('{{$key}}')" spinner="deleteDetail" type="button" class="btn-error" icon="o-x-mark" /></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="100">No items found</td>
                    </tr>
                    @endforelse

                    </tbody>
                    </table>
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
