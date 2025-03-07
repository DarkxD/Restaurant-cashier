<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Importáld a DomPDF osztályt

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        // Adatok validálása
        $request->validate([
            'client_id' => 'required|integer',
            'cashier_id' => 'required|integer',
            'status' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price_netto' => 'required|numeric',
            'items.*.vat' => 'required|numeric',
        ]);
    
        // Számla létrehozása
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . time(),
            'client_id' => $request->client_id,
            'cashier_id' => $request->cashier_id,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'issue_time' => now(),
            'total_price' => 0,
        ]);
    
        $totalPrice = 0;
    
        // Termékek hozzáadása
        foreach ($request->items as $item) {
            $unitPriceNetto = $item['unit_price_netto'];
            $quantity = $item['quantity'];
            $vat = $item['vat'];
    
            // Bruttó ár számítása (netto ár + ÁFA)
            $totalPriceItem = ($unitPriceNetto * (1 + $vat / 100)) * $quantity;
            $totalPrice += $totalPriceItem;
    
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $item['item_id'],
                'quantity' => $quantity,
                'unit_price_netto' => $unitPriceNetto,
                'vat' => $vat,
                'total_price' => $totalPriceItem,
            ]);
        }
    
        // Összesített ár frissítése
        $invoice->update(['total_price' => $totalPrice]);
    
        // Kapcsolódó adatok lekérése
        $invoice->load('client', 'cashier', 'items.item');
    
        // PDF generálása DomPDF segítségével
        $pdf = Pdf::loadView('invoice', compact('invoice'));
    
        // PDF megjelenítése a böngészőben
        return $pdf->stream('invoice.pdf');
    }
}