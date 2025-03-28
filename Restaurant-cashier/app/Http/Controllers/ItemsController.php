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
        $items = Item::with(['category', 'tags'])->get();
        
        // Relatív útvonalak alapján generáljuk a teljes URL-eket
        $items->transform(function ($item) {
            // Főkép URL-je, ha létezik
            $item->image = $item->image && Storage::disk('public')->exists($item->image)
                ? Storage::url($item->image)
                : null;

            // Album képek URL-jei, ha léteznek
            $item->album = json_decode($item->album, true) ?? [];
            $item->album = array_map(function ($albumImage) {
                return $albumImage && Storage::disk('public')->exists($albumImage)
                    ? Storage::url($albumImage)
                    : null;
            }, $item->album);

            // Tag-ek neveinek tömbbé alakítása
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
    public function store(Request $request, $id = null)
    {
        // Hibakezelés és validáció
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price_brutto' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'album.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array', // A tags tömb opcionális
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }

        // Elem keresése vagy új létrehozása
        $item = $id ? Item::find($id) : new Item;

        if ($id && !$item) {
            return response()->json([
                'message' => "A tétel nem található.",
                'status' => 404,
            ]);
        }

        // Főkép feltöltése
        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            // Régi kép törlése, ha létezik
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $image = $request->file('image');
            $imageName = $this->generateFileName($request->input('name'), $image->getClientOriginalName());
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        // Album képek feltöltése
        $albumPaths = $item->album ? json_decode($item->album, true) : [];
        if ($request->hasFile('album')) {
            // Régi album képek törlése
            foreach ($albumPaths as $oldImage) {
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $albumPaths = [];
            foreach ($request->file('album') as $file) {
                $albumName = $this->generateFileName($request->input('name'), $file->getClientOriginalName());
                $path = $file->storeAs('images', $albumName, 'public');
                $albumPaths[] = $path;
            }
        }

        // Elem adatainak frissítése
        $item->name = $request->input('name');
        $item->description = $request->input('description');
        $item->short_name = $request->input('short_name');
        $item->image = $imagePath;
        $item->album = json_encode($albumPaths);
        $item->price_netto = $request->input('price_netto');
        $item->price_brutto = $request->input('price_brutto');
        $item->default_vat = $request->input('default_vat');
        $item->show_cashier = $request->input('show_cashier', true);
        $item->show_menu = $request->input('show_menu', false);
        $item->category_id = $request->category_id;

        // Elem mentése
        $item->save();

        // Tag-ek hozzárendelése
        if ($request->has('tags')) {
            // Ha a tags tömb nem üres, akkor szinkronizáljuk a címkéket
            $item->tags()->sync($request->tags);
        } else {
            // Ha a tags tömb üres vagy hiányzik, akkor minden címkét eltávolítunk
            $item->tags()->detach();
        }

        // Válasz visszaadása
        return response()->json([
            'message' => $id ? 'Tétel sikeresen frissítve' : 'Tétel sikeresen létrehozva',
            'status' => 200,
            'item' => $item,
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
    public function edit($id)
    {
        $item = Item::with(['category', 'tags'])->find($id);

        if ($item) {
            // Főkép URL-je, ha létezik
            $item->image = $item->image && Storage::disk('public')->exists($item->image)
                ? Storage::url($item->image)
                : null;

            // Album képek URL-jei, ha léteznek
            $item->album = json_decode($item->album, true) ?? [];
            $item->album = array_map(function ($albumImage) {
                return $albumImage && Storage::disk('public')->exists($albumImage)
                    ? Storage::url($albumImage)
                    : null;
            }, $item->album);

            return response()->json([
                'status' => 200,
                'item' => $item,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'A tétel nem található.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        return $this->store($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);

        if ($item) {
            // Főkép törlése
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            // Album képek törlése
            if ($item->album) {
                $albumImages = json_decode($item->album, true);
                foreach ($albumImages as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $item->delete();

            return response()->json([
                'message' => "Termék tétel törölve: " . $item->name . " id: " . $id,
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'message' => "A tétel nem található.",
                'status' => 404,
            ]);
        }
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


    public function menu()
    {
        $categories = Category::whereHas('items', function($query) {
            $query->where('show_menu', true);
        })->with(['items' => function($query) {
            $query->where('show_menu', true)->with('tags');
        }])->get();

        return view('menu', compact('categories'));
    }


    public function toggleMenuVisibility(Request $request, $id)
    {
        $request->merge([
            'show_menu' => filter_var($request->show_menu, FILTER_VALIDATE_BOOLEAN)
        ]);

        $request->validate([
            'show_menu' => 'required|boolean'
        ]);

        $item = Item::findOrFail($id);
        $item->show_menu = $request->show_menu;
        $item->save();

        return response()->json([
            'success' => true,
            'show_menu' => $item->show_menu,
            'item_id' => $item->id,
            'message' => 'Menu visibility updated successfully'
        ]);
    }

    public function getAllItemsForAdmin()
    {
        $categories = Category::with(['items' => function($query) {
            $query->orderBy('name')->with('tags');
        }])->get();

        return response()->json([
            'categories' => $categories
        ]);
    }
    public function getItem(Item $item)
    {
        $item->load(['category', 'tags']);
        
        // Kép URL-ek formázása
        $item->image = $item->image ? Storage::url($item->image) : null;
        
        return response()->json($item);
    }

}
