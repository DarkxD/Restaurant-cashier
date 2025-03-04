<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'short_name', 'image', 'album',
        'show_cashier', 'show_menu', 'price_netto', 'price_brutto', 'default_vat'
    ];

    protected $casts = [
        'album' => 'array',
        'show_cashier' => 'boolean',
        'show_menu' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }
}
