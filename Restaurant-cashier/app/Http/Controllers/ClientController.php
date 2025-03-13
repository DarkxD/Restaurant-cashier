<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function fetchClients(){
        $clientUsers = Client::all();
        return response()->json([
            'clientUsers' => $clientUsers,
        ]);
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
        // Véletlenszerű név és szín generálása
        function randomName() {
            $names = array(
                'Juan',
                'Luis',
                'Pedro',
                'Andrew',
                'Albert',
                'Anthony',
                'Arthur',
                'Bernard',
                'Charles',
                'Christopher',
                'Daniel',
                'Donald',
                'Edward',
                'Eugene',
                'Francis',
                'Frederick',
                'Henry',
                'Irving',
                'James',
                'Joseph',
                'John',
                'Lawrence',
                'Leonard',
                'Nathan',
                'Nicholas',
                'Patrick',
                'Peter',
                'Raymond',
                'Richard',
                'Robert',
                'Ronald',
                'Russell',
                'Samuel',
                'Stephan',
                'Stuart',
                'Theodore',
                'Thomas',
                'Timothy',
                'Walter',
                'William',
            );
            return $names[rand ( 0 , count($names) -1)];
        }

        $randomName = randomName();
        
        $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Véletlenszerű hex színkód
    
        // Új ügyfél létrehozása
        $client = new Client;
        $client->name = $randomName;
        $client->color = $randomColor;
        $client->status = 'open';
        $client->save();

        $clientID = $client->id;
    
        // Átirányítás a megfelelő oldalra
        return response()->json([
            'status'=>200,
            'clientID'=>$clientID,
        ]);
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
            'name' => 'required|string',
            'iranyitoszam' => 'nullable|string',
            'telepules' => 'nullable|string',
            'utca_hazszam' => 'nullable|string',
            'note' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);
    
        $client->update($request->all());
    
        return response()->json([
            'status' => 'success',
            'message' => 'Ügyfél adatai sikeresen frissítve!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('ugyfelek.index')->with('success', 'Ügyfél sikeresen törölve!');
    }


    public function createNewClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $client = Client::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'client' => $client,
        ]);
    }
}
