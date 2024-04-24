<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Country;

class CountryForm extends Form
{
    public ?Country $country;
    public $editMode = false;

    #[Validate('required')]
    public $name = '';

    public function set(Country $country): void
    {
        $this->country = $country;
        $this->editMode = true;
        $this->name = $country->name;
    }

    public function save(): void
    {
        if ($this->editMode) {
            $this->editMode = false;
            $this->country->update($this->all());

        } else {
            Country::create($this->all());
        }

        $this->reset();
    }

    public function init(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}
