<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.items.index');
    }

    /**
     * Show the form for creating a new resource.
     */



    public function fetchItems(){
        $items = Item::all();
        return response()->json([
            'items' => $items,
        ]);
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'price_brutto' => 'required',
        ], [
            'name.required' => 'Név megadása szükséges.',
            'image.required' => 'Kép megadása szükséges.',
            'price_brutto.required' => 'Bruttó ár megadása szükséges.',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),

            ]);
        } else {
                    // Új tétel létrehozása és mentése
            $item = new Item;
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->short_name = $request->input('short_name');
            $item->image = $request->input('image');
            $item->album = $request->input('album');
            $item->price_netto = $request->input('price_netto');
            $item->price_brutto = $request->input('price_brutto');
            $item->default_vat = $request->input('default_vat');
            $item->show_cashier = $request->input('show_cashier', true); // Alapértelmezett érték: false
            $item->show_menu = $request->input('show_menu', false); // Alapértelmezett érték: false
            $item->save();

            // Sikeres válasz küldése
            return response()->json([
                'message' => 'Tétel sikeresen létrehozva',
                'status' => 200,
                'item' => $item,
            ]);
            };
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteItem($id){
        $item = Item::find($id);
        $name = $item->nev;
        $item->delete();

        return response()->json([
            'message'=>"Termék tétel törölve: " . $name . " id: " . $id,
            'status'=>200,
        ]);
    }
}
