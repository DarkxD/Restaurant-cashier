<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Models\CashierUsers;
use Illuminate\Support\Facades\Hash;

class PinkodAuthenticated
{
    public function handle($request, Closure $next)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        if (!Session::has('pinkod_authenticated')) {
            return redirect()->route('login'); // Ha nincs bejelentkezve, irányítsuk a bejelentkezési oldalra
        }

        // Ha be van jelentkezve, engedjük tovább a kérést
        return $next($request);
    }
}
