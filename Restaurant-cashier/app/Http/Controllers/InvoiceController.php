<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf; // Importáld a DomPDF osztályt
use Carbon\Carbon;


class InvoiceController extends Controller
{
    /* public function createInvoice(Request $request)
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
    } */
    /* public function printInvoice($id)
    {
        $invoice = Invoice::with(['client', 'items.item'])->find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Számla nem található'], 404);
        }

        // PDF generálása
        $pdf = Pdf::loadView('reports.receipt', compact('invoice'));

        // PDF megjelenítése a böngészőben
        return $pdf->stream('invoice.pdf');
    } */


    public function getDataForReceiptByClient($clientId)
    {
        $client = Client::with(['invoices.items.item', 'invoices.cashier'])->find($clientId);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        // Az első számla adatait használjuk (vagy válaszd ki a megfelelőt)
        $invoice = $client->invoices->first();

        if (!$invoice) {
            return response()->json(['error' => 'No invoice found for this client'], 404);
        }

        return response()->json([
            'invoice' => $invoice,
            'invoiceItems' => $invoice->items,
            'cashier' => $invoice->cashier,
        ]);
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


    public function createNewInvoice(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'cashier_id' => 'required|integer',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . time(),
            'client_id' => $request->client_id,
            'cashier_id' => $request->cashier_id,
            'status' => 'open',
            'payment_method' => 'none',
            'total_price' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'invoice' => $invoice,
        ]);
    }

    public function moveItemsToNewInvoice(Request $request)
    {
        $request->validate([
            'original_invoice_id' => 'required|integer',
            'new_invoice_id' => 'required|integer',
            'items' => 'required|array',
        ]);
 
        $originalInvoice = Invoice::find($request->original_invoice_id);
        $newInvoice = Invoice::find($request->new_invoice_id);

        if (!$originalInvoice || !$newInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Számla nem található.',
            ], 404);
        }

        foreach ($request->items as $itemId) {
            $invoiceItem = InvoiceItem::find($itemId);

            if ($invoiceItem) {
                $invoiceItem->update([
                    'invoice_id' => $newInvoice->id,
                ]);
            }
        }

        // Összesített ár frissítése mindkét számlán
        $originalInvoice->update([
            'total_price' => $originalInvoice->items->sum('total_price'),
        ]);

        $newInvoice->update([
            'total_price' => $newInvoice->items->sum('total_price'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tételek sikeresen átmozgatva.',
        ]);
    }


    public function closeInvoice(Request $request)
        {
            $request->validate([
                'client_id' => 'required|integer',
                'invoice_id' => 'required|integer',
                'payment_method' => 'required|string',
            ]);

            $currentTime = Carbon::now()->format('Y-m-d H:i:s');

            $client = Client::find($request->client_id);
            $invoice = Invoice::find($request->invoice_id);

            if ($client && $invoice) {
                $invoice->update([
                    'status' => 'closed',
                    'payment_method' => $request->payment_method,
                    'issue_time' => $currentTime,
                    
                ]);

                $client->update([
                    'status' => 'closed',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Számla sikeresen lezárva.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Számla vagy ügyfél nem található.',
                ], 404);
            }
        }

        public function deleteInvoice(Request $request)
        {
            $request->validate([
                'client_id' => 'required|integer',
                'invoice_id' => 'required|integer',
            ]);

            $client = Client::find($request->client_id);
            $invoice = Invoice::find($request->invoice_id);

            if ($client && $invoice) {
                // Töröljük az invoice_items táblából a tételeket
                InvoiceItem::where('invoice_id', $invoice->id)->delete();

                // Töröljük a számlát
                $invoice->delete();

                // Töröljük az ügyfelet
                $client->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Számla és ügyfél sikeresen törölve.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Számla vagy ügyfél nem található.',
                ], 404);
            }
        }
};