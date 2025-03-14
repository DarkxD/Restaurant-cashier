<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;

class ReceiptController extends Controller
{
    public function getReceiptDataByClient($clientId)
    {
        // Az ügyfél lekérése az adatbázisból
        $client = Client::find($clientId);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        // Az ügyfélhez tartozó első számla lekérése
        $invoice = Invoice::with(['items.item', 'cashier'])
            ->where('client_id', $clientId)
            ->first();

        if (!$invoice) {
            return response()->json(['error' => 'No invoice found for this client'], 404);
        }

        // A receipt.blade.php tartalmának renderelése és változók átadása
        $html = view('reports.receipt', [
            'invoice' => $invoice,
            'invoiceItems' => $invoice->items,
            'cashier' => $invoice->cashier,
        ])->render();

        return response()->json(['html' => $html]);
    }
}