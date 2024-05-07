<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Item;

class ItemController extends Controller
{
    public function export(Request $request)
    {
        $items = Item::get();

        $writer = SimpleExcelWriter::streamDownload('items.xlsx');

        foreach ( $items->lazy() as $item ) {
            $writer->addRow([
                'name'      => $contact->name ?? '',
                'price'     => $contact->price ?? '',
            ]);
        }

        $writer->toBrowser();
    }
}
