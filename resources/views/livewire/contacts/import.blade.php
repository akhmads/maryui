<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Spatie\SimpleExcel\SimpleExcelReader;
use Mary\Traits\Toast;
use App\Models\Contact;

new class extends Component {
    use Toast, WithFileUploads;

    public $file;

    public function save()
    {
        $valid = $this->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $status = $this->file->store('/', 'local');
        $target = storage_path('app/'.$status);

        DB::beginTransaction();

        try {

            if ( file_exists( $target ) ) {

                $rows = SimpleExcelReader::create($target)->getRows();

                $rows->each(function(array $row) {
                    Contact::insert([
                        'name' => $row['name'],
                        'address' => $row['address'],
                        'email' => $row['email'],
                        'phone' => $row['phone'],
                        'mobile' => $row['mobile'],
                        'status' => 'active',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                });
            }

            DB::commit();
            $this->success('Contact has been imported.', redirectTo: '/contacts');
        }
        catch (Exception $e)
        {
            DB::rollBack();
            $this->success('Contact fail to import.', redirectTo: '/contacts/import');
        }
    }
}; ?>

<div>
    <x-header title="Import contacts" separator />
    <x-card>
        <x-form wire:submit="save">
            <x-file wire:model="file" label="Contact File" hint="xlsx or csv" />
            <x-slot:actions>
                <x-button label="Cancel" link="/contacts" />
                <x-button label="Import" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
