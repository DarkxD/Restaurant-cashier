<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CashierUsers extends Model
{
    use HasFactory;

    protected $fillable = [
        'pinkod',
        'nev',
        'jogosultsag',
    ];

    // Pinkód hash-elése mentés előtt
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pinkod) {
            $pinkod->pinkod = Hash::make($pinkod->pinkod);
        });
    }
}
