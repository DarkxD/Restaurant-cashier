<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Tag;

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



     public function fetchItems()
     {
         //$items = Item::all();
         $items = Item::with(['category', 'tags'])->get();
         
         // Relatív útvonalak alapján generáljuk a teljes URL-eket
         $items->transform(function ($item) {
            $item->image = Storage::url($item->image); // Főkép URL-je
            $item->album = json_decode($item->album); // Album képek tömbje
            $item->album = array_map(function ($albumImage) {
                return Storage::url($albumImage); // Album képek URL-jei
            }, $item->album);
            // Tag-ek neveinek tömbbé alakítása
            //$item->tag_names = $item->tags->pluck('name')->toArray();
            $item->category_name = $item->category ? $item->category->name : 'Nincs kategória';
            return $item;
         });

         $categories = Category::all();
         $tags = Tag::all();  
     
         
         return response()->json([
             'items' => $items,
             'categories' => $categories,
             'tags' => $tags,
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
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price_brutto' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'album.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array', 
            'tags.*' => 'integer|exists:tags,id' 
        ], [
            'name.required' => 'Név megadása szükséges.',
            'price_brutto.required' => 'Bruttó ár megadása szükséges.',
            'price_brutto.numeric' => 'A bruttó ár számnak kell lennie.',
            'image.image' => 'A fájlnak képnek kell lennie.',
            'image.mimes' => 'Csak JPEG, PNG, JPG és GIF formátumú képek tölthetők fel.',
            'image.max' => 'A kép mérete nem lehet nagyobb 2 MB-nál.',
            'album.*.image' => 'Az album képeinek képnek kell lenniük.',
            'album.*.mimes' => 'Csak JPEG, PNG, JPG és GIF formátumú képek tölthetők fel az albumba.',
            'album.*.max' => 'Az album képeinek mérete nem lehet nagyobb 2 MB-nál.',
        ]);
        

            // Főkép feltöltése
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $this->generateFileName($request->input('name'), $image->getClientOriginalName());
                $imagePath = $image->storeAs('images', $imageName, 'public');
                //$imagePath = Storage::disk('public')->url($imagePath);
            }


            // Album képek feltöltése
            $albumPaths = [];
            if ($request->hasFile('album')) {
                foreach ($request->file('album') as $file) {
                    $albumName = $this->generateFileName($request->input('name'), $file->getClientOriginalName());
                    $path = $file->storeAs('images', $albumName, 'public');
                    $albumPaths[] = $path;
                    //$albumPaths[] = Storage::disk('public')->url($path);
                }
            }




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
            $item->image = $imagePath; // Főkép URL-je
            $item->album = json_encode($albumPaths); // Album képek URL-jei JSON formátumban
            $item->price_netto = $request->input('price_netto');
            $item->price_brutto = $request->input('price_brutto');
            $item->default_vat = $request->input('default_vat');
            $item->show_cashier = $request->input('show_cashier', true); // Alapértelmezett érték: false
            $item->show_menu = $request->input('show_menu', false); // Alapértelmezett érték: false
            $item->category_id = $request->category_id;

            $item->save();

            // Tag-ek hozzárendelése
            if ($request->has('tags')) {
                $item->tags()->sync($request->tags);
            }

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


    private function generateFileName($productName, $originalName)
    {
        // Dátum formázása
        $date = now()->format('YmdHis');

        // Terméknév formázása (slug)
        $slug = Str::slug($productName);

        // Fájlnév összeállítása
        return $date . '_' . $slug . '_' . $originalName;
    }

}
