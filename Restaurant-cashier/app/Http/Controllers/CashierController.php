<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\CashierUsers;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $client = Client::find($id);
        $categories = Category::where('show_cashier', true)->get();
        $cashier = CashierUsers::find(session('cashier_id')); // Kasszás azonosítója a session-ből

        $client = Client::find($id);
        $categories = Category::where('show_cashier', true)->get();
        $cashier = CashierUsers::find(session('cashier_id')); // Kasszás azonosítója a session-ből

        // Nyitott számla lekérése az ügyfélhez
        $invoice = Invoice::where('client_id', $client->id)
                        ->where('status', 'open', 'closed')
                        ->first();

        // Számla tételeinek lekérése
        $invoiceItems = $invoice ? $invoice->items : collect();

        return view('cashier', [
            'client' => $client,
            'categories' => $categories,
            'cashier' => $cashier,
            'invoiceItems' => $invoiceItems, // Számla tételei átadva a nézetnek
            'invoice' => $invoice, // Számla átadva a nézetnek
        ]);

        
    }



    public function getDataForReceipt($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Számla nem található'], 404);
        }

        $invoiceItems = $invoice->items;
        $totalVat = 0;

        // Számítsd ki az adó összegét
        foreach ($invoiceItems as $invoiceItem) {
            $vatAmount = ($invoiceItem->total_price - $invoiceItem->unit_price_netto);
            $totalVat += $vatAmount;
        }

        return response()->json([
            'invoice' => [
                'invoice_number' => $invoice->invoice_number,
                'issue_time' => $invoice->issue_time ?? now()->format('Y-m-d H:i:s'),
                'total_price' => $invoice->total_price,
                'total_vat' => $totalVat,
                'net_price' => $invoice->total_price - $totalVat,
            ],
            'cashier' => [
                'nev' => $invoice->cashier->nev,
            ],
            'invoiceItems' => $invoiceItems->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'name' => $item->item->name,
                    'total_price' => $item->total_price,
                ];
            }),
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /* public function deleteInvoiceItem($id)
    {
        $invoiceItem = InvoiceItem::find($id);

        if ($invoiceItem) {
            // Tétel törlése
            $invoiceItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Tétel sikeresen törölve.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'A tétel nem található.',
            ], 404);
        }
    } */
}
