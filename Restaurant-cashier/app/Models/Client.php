<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'status',
        'iranyitoszam',
        'telepules',
        'utca_hazszam',
        'note',
        'email',
        'phone',
    ];
}
