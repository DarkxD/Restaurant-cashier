<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ugyfelek = Client::all();
        return view('home.index', compact('ugyfelek'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ugyfelek.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            /* 'nev' => 'required',
            'statusz' => 'required', */
        ]);

        Client::create($request->all());

        return redirect()->route('cashier')->with('success', 'Ügyfél sikeresen létrehozva!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            /* 'nev' => 'required',
            'statusz' => 'required', */
        ]);

        $client->update($request->all());

        return redirect()->route('cashier')->with('success', 'Ügyfél sikeresen frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('ugyfelek.index')->with('success', 'Ügyfél sikeresen törölve!');
    }
}
