<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'client_id', 'cashier_id', 'status', 'payment_method', 'issue_time', 'total_price'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function cashier()
    {
        return $this->belongsTo(CashierUsers::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}