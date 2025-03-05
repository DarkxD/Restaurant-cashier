<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'short_name', 'image', 'album',
        'show_cashier', 'show_menu', 'price_netto', 'price_brutto', 'default_vat', 'category_id'
    ];

    protected $casts = [
        'album' => 'array',
        'show_cashier' => 'boolean',
        'show_menu' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'item_tag');
    }
}
