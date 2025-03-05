<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'image', 'show_cashier'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
