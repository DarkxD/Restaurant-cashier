<?php

namespace App\Http\Controllers;

use App\Models\CashierUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class PinkodAuthController extends Controller
{
    // Pinkód beviteli űrlap megjelenítése
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Pinkód ellenőrzése és bejelentkezés
    public function login(Request $request)
    {
        // Validáljuk a bemeneti adatokat
        $request->validate([
            'pinkod' => 'required|string',
        ]);

        // Pinkód ellenőrzése az adatbázisban
        $pinkod = $request->input('pinkod');
        $osszesPinkod = CashierUsers::all(); // Az összes pinkód lekérése

        foreach ($osszesPinkod as $felhasznalo) {
            if (Hash::check($pinkod, $felhasznalo->pinkod)) {
                // Pinkód helyes, mentjük a session-be
                Session::put('pinkod_authenticated', true);
                Session::put('felhasznalo_nev', $felhasznalo->nev);
                Session::put('felhasznalo_jogosultsag', $felhasznalo->jogosultsag);
                return redirect()->route('home');
            }
        }

        // Pinkód hibás, visszairányítjuk a bejelentkezési oldalra
        return back()->withErrors(['pinkod' => 'Hibás pinkód!']);
    }

    // Kijelentkezés
    public function logout()
    {
        // Töröljük a session adatokat
        Session::forget('pinkod_authenticated');
        Session::forget('felhasznalo_nev');
        Session::forget('felhasznalo_jogosultsag');
        return redirect()->route('login');
    }

    // Védett tartalom megjelenítése
    public function home()
    {
        if(session()->get('felhasznalo_jogosultsag') == "administrator"){
            return view('home');
        } else {
            return view(view: 'home');    
        }
        
    }
    public function cashier()
    {
        return view('cashier');
    }

    public function getaccess(){
        if(session()->get('felhasznalo_jogosultsag') == "administrator"){
            return 1;
        }
    }
    
}