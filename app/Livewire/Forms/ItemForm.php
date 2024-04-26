<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Enums\ActiveStatus;
use App\Helpers\Cast;
use App\Models\Item;

class ItemForm extends Form
{
    public ?Item $item;
    public $editMode = false;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $price;

    #[Validate('required')]
    public $status = ActiveStatus::active;

    public function set(Item $item): void
    {
        $this->item = $item;
        $this->editMode = true;
        $this->name = $item->name;
        $this->price = $item->price;
        $this->status = $item->status;
    }

    public function save(): void
    {
        if ($this->editMode) {
            $this->editMode = false;
            $this->item->update($this->all());

        } else {
            Item::create($this->all());
        }

        $this->reset();
    }

    public function init(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    public function beforeValidation(): void
    {
        $this->price = Cast::number($this->price);
    }
}
