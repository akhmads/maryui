<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Helpers\Cast;
use App\Models\SalesInvoice;
use App\Models\Contact;
use App\Models\Item;

new class extends Component {
    use Toast;

    public SalesInvoice $salesInvoice;

    public string $code = '';
    public string $date = '';
    public ?int $contact_id = null;
    public Collection $details;
    public Collection $contactSearchable;
    public Collection $itemSearchable;
    public $total_qty = 0;
    public $total_dpp = 0;
    public $total_invoice = 0;
    public $contacts = [];

    public function mount(): void
    {
        $this->fill($this->salesInvoice);
        $this->details = collect([]);
        $this->fillDetail();
        $this->searchContact();
        $this->searchItem();
    }

    public function with(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => 'required',
            'date' => 'required',
            'contact_id' => 'required',
            'details' => 'array|min:1',
            'details.*.item_id' => 'required',
            'details.*.qty' => 'required|gt:0',
            'details.*.price' => 'required|gt:0',
        ]);

        unset($data['details']);
        $data['total_qty'] = Cast::number($this->total_qty);
        $data['total_dpp'] = Cast::number($this->total_dpp);
        $data['total_invoice'] = Cast::number($this->total_invoice);

        $this->salesInvoice->update($data);

        $this->saveDetail();

        $this->success('Invoice has been updated.', redirectTo: '/sales-invoice');
    }

    // public function searchContact(string $value = ''): void
    // {
    //     $selectedOption = Contact::where('id', $this->contact_id)->get();
    //     $this->contactSearchable = Contact::query()
    //         ->where('name', 'like', "%$value%")
    //         ->take(5)
    //         ->orderBy('name')
    //         ->get()
    //         ->merge($selectedOption);
    // }

    public function searchContact(string $value = ''): void
    {
        $contacts = Contact::where('name', 'like', "%$value%")->orderBy('name')->limit(10)->get();
        $results = [];
        foreach ($contacts as $contact) {
            $results[$contact->id] = $contact->name;
        }
        $this->contacts = $results;
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
            'item_id' => '',
            'qty' => 0,
            'price' => 0,
            'subtotal' => 0,
        ]);
    }

    public function deleteDetail($key)
    {
        $this->details->forget($key);
        $this->total();
    }

    public function fillDetail()
    {
        $details = $this->salesInvoice->details()->get();
        foreach ($details as $detail)
        {
            $this->details->push([
                'item_id' => $detail->item_id,
                'qty' => Cast::number($detail->qty),
                'price' => Cast::number($detail->price),
                'subtotal' => Cast::number($detail->subtotal),
            ]);
        }
    }

    public function saveDetail()
    {
        $this->salesInvoice->details()->delete();
        foreach ($this->details->all() as $detail)
        {
            $this->salesInvoice->details()->create([
                'item_id' => $detail['item_id'],
                'qty' => Cast::number($detail['qty']),
                'price' => Cast::number($detail['price']),
                'subtotal' => Cast::number($detail['subtotal']),
            ]);
        }
    }

    public function updated($property, $value): void
    {
        $props = explode('.',$property);
        $index = $props[1] ?? '';
        $field = $props[2] ?? '';

        if (in_array($field, ['price','qty']))
        {
            $data = $this->details->get($index);
            $data['subtotal'] = Cast::number($data['qty']) * Cast::number($data['price']);
            $this->details->put($index, $data);

            $this->total();
        }
    }

    public function total(): void
    {
        $this->total_qty = Cast::number($this->details->sum('qty'));
        $this->total_dpp = Cast::number($this->details->sum('subtotal'));
        $this->total_invoice = $this->total_dpp;
    }
}; ?>

<div>
    <x-header title="Edit Sales Invoice" separator />
    <x-form wire:submit="save">
        <div class="grid grid-cols-12 gap-6">
            <x-card class="col-span-12">
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-6">
                        <x-input label="Code" wire:model="code" />
                        <x-datetime label="Date" wire:model="date" />
                        {{-- <x-choices label="Contact" wire:model="contact_id" :options="$contactSearchable" search-function="searchContact" single searchable /> --}}
                        <x-choices2 label="Contact" wire:model="contact_id" :options="$contacts" search="searchContact(value)" />
                    </div>
                </div>
            </x-card>
            <x-card class="col-span-12">
                <div class="space-y-4">
                    <!-- TABLE DETAIL -->
                    <div class="flex justify-end">
                        <x-button wire:click="addDetail" label="Add Detail" icon="o-plus" spinner="addDetail" type="button" class="btn" />
                    </div>

                    @error('details')
                    <div class="text-error text-sm">{{ $message }}</div>
                    @enderror

                    <table class="table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th class="w-[200px]">Price</th>
                        <th class="w-[150px]">Qty</th>
                        <th class="w-[200px]">Subtotal</th>
                        <th class="w-[80px]">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    {{-- x-mask:dynamic="$money($input, '.', ',')" --}}

                    @forelse ( $details->all() as $key => $detail )
                    <tr wire:key="item-detail-{{ $key }}">
                        <td>
                            <x-choices label="" wire:model.live.debounce="details.{{$key}}.item_id" :options="$itemSearchable" search-function="searchItem" single searchable />
                            @error("details.{{$key}}.item_id")<div class="text-error text-sm">{{ $message }}</div>@enderror
                        </td>
                        <td>
                            <x-input label="" wire:model.live.debounce.500ms="details.{{$key}}.price" class="text-right" />
                            @error("details.{{$key}}.price")<div class="text-error text-sm">{{ $message }}</div>@enderror
                        </td>
                        <td>
                            <x-input label="" wire:model.live.debounce.500ms="details.{{$key}}.qty" class="text-right" />
                            @error("details.{{$key}}.qty")<div class="text-error text-sm">{{ $message }}</div>@enderror
                        </td>
                        <td>
                            <x-input label="" wire:model.live="details.{{$key}}.subtotal" class="text-right" readonly />
                            @error("details.{{$key}}.subtotal")<div class="text-error text-sm">{{ $message }}</div>@enderror
                        </td>
                        <td><x-button wire:click="deleteDetail('{{$key}}')" spinner="deleteDetail('{{$key}}')" type="button" class="btn-error btn-sm" icon="o-x-mark" /></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="100">No items found</td>
                    </tr>
                    @endforelse

                    <tr>
                        <td colspan="3" class="text-right font-semibold">Subtotal</td>
                        <td class="">
                            <x-input label="" wire:model.live="total_dpp" class="text-right" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right font-semibold">Total</td>
                        <td class="">
                            <x-input label="" wire:model.live="total_invoice" class="text-right" readonly />
                        </td>
                    </tr>
                    </tbody>
                    </table>
                </div>
            </x-card>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" link="/sales-invoice" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
