<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckCashier
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('pinkod_authenticated')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
