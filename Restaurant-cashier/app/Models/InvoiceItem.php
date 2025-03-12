<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'item_id', 'quantity', 'unit_price_netto', 'vat', 'total_price'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }


    // Bruttó ár kiszámítása
    public function getTotalPriceBruttoAttribute()
    {
        return $this->total_price * (1 + $this->vat / 100);
    }

    // Nettó ár kiszámítása
    public function getTotalPriceNettoAttribute()
    {
        return $this->total_price;
    }

    // ÁFA összeg kiszámítása
    public function getVatAmountAttribute()
    {
        return $this->total_price * ($this->vat / 100);
    }
}