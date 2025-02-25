<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nev',
        'szin',
        'statusz',
        'iranyitoszam',
        'telepules',
        'utca',
        'hazszam',
        'megjegyzes',
    ];
}
