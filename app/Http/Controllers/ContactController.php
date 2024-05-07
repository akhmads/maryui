<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Contact;

class ContactController extends Controller
{
    public function export(Request $request)
    {
        $contacts = Contact::get();

        $writer = SimpleExcelWriter::streamDownload('contacts.xlsx');

        foreach ( $contacts->lazy() as $contact ) {
            $writer->addRow([
                'name'      => $contact->name ?? '',
                'address'   => $contact->address ?? '',
                'email'     => $contact->email ?? '',
                'phone'     => $contact->phone ?? '',
                'mobile'    => $contact->mobile ?? '',
            ]);
        }

        $writer->toBrowser();
    }
}
