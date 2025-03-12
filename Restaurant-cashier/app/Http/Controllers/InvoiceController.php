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





    public function addItemToInvoice(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'item_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'cashier_id' => 'required|integer',
            'vat' => 'required',
        ]);

        // Ellenőrizzük, hogy van-e már nyitott számla az ügyfélnek
        $invoice = Invoice::firstOrCreate([
            'client_id' => $request->client_id,
            'status' => 'open',
        ], [
            'invoice_number' => 'INV-' . time(),
            'cashier_id' => $request->cashier_id,
            'payment_method' => 'cash',
            'issue_time' => now(),
            'total_price' => 0,
        ]);

        // Termék lekérése
        $item = Item::find($request->item_id);

        // Bruttó ár kiszámítása
        $unitPriceBrutto = $item->price_brutto;
        $vatRate = $request->vat;
        $unitPriceNetto = $unitPriceBrutto / (1 + $vatRate / 100); // Nettó ár kiszámítása
        $totalPrice = $item->price_brutto * $request->quantity;

        // Termék hozzáadása a számlához
        $invoiceItem = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'quantity' => $request->quantity,
            'unit_price_netto' => $unitPriceNetto,
            'vat' => $vatRate,
            'total_price' => $totalPrice,
        ]);

        // Összesített ár frissítése
        $invoice->update([
            'total_price' => $invoice->total_price + $totalPrice,
        ]);

        // Számla újratöltése az aktuális tételekkel
        $invoice->load('items');

        return response()->json([
            'status' => 'success',
            'message' => 'Termék sikeresen hozzáadva a számlához.',
            'invoiceItem' => $invoiceItem,
            'item' => $item,
            'invoice' => $invoice, // Visszaküldjük az frissített számlát
            'unit_price_netto' => round($invoiceItem->unit_price_netto, 0),
            'szamolt_vat_ertek' => round($invoiceItem->total_price - $invoiceItem->unit_price_netto, 0),
        ]);
    }





    public function deleteInvoiceItem($id)
    {
        $invoiceItem = InvoiceItem::find($id);
    
        if ($invoiceItem) {
            // Számla lekérése
            $invoice = $invoiceItem->invoice;
    
            // Tétel törlése
            $invoiceItem->delete();
    
            // Összesített ár frissítése
            $invoice->update([
                'total_price' => $invoice->items->sum('total_price'),
            ]);
    
            // Számla újratöltése az aktuális tételekkel
            $invoice->load('items');
    
            return response()->json([
                'status' => 'success',
                'message' => 'Tétel sikeresen törölve.',
                'invoice' => $invoice, // Visszaküldjük az frissített számlát
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'A tétel nem található.',
            ], 404);
        }
    }



    
};