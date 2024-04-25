<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Enums\ActiveStatus;
use App\Models\Contact;

class ContactForm extends Form
{
    public ?Contact $contact;
    public $editMode = false;

    #[Validate('required')]
    public $name;

    #[Validate('nullable')]
    public $address;

    #[Validate('nullable|email')]
    public $email;

    #[Validate('nullable')]
    public $phone;

    #[Validate('nullable')]
    public $mobile;

    #[Validate('required')]
    public $status = ActiveStatus::active;

    public function set(Contact $contact): void
    {
        $this->contact = $contact;
        $this->editMode = true;
        $this->name = $contact->name;
        $this->address = $contact->address;
        $this->email = $contact->email;
        $this->phone = $contact->phone;
        $this->mobile = $contact->mobile;
        $this->status = $contact->status;
    }

    public function save(): void
    {
        if ($this->editMode) {
            $this->editMode = false;
            $this->contact->update($this->all());

        } else {
            Contact::create($this->all());
        }

        $this->reset();
    }

    public function init(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}
