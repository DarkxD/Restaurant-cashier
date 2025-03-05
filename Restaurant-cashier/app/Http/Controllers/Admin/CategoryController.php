<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'image' => 'nullable|image',
            'show_cashier' => 'boolean',
        ]);

        // Kép feltöltése
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $this->generateFileName($request->input('name'), $image->getClientOriginalName());
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        // Kategória létrehozása
        $category = Category::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath, // Kép elérési útjának mentése
            'show_cashier' => $request->input('show_cashier', false), // Alapértelmezett érték false
        ]);

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'image' => 'nullable|image',
            'show_cashier' => 'boolean',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Kép feltöltése, ha van új kép
        $imagePath = $category->image; // Megtartjuk a régi képet, ha nincs új feltöltve
        if ($request->hasFile('image')) {
            // Régi kép törlése, ha létezik
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Új kép feltöltése
            $image = $request->file('image');
            $imageName = $this->generateFileName($request->input('name'), $image->getClientOriginalName());
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        // Kategória frissítése
        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath, // Kép elérési útjának mentése
            'show_cashier' => $request->input('show_cashier', false), // Alapértelmezett érték false
        ]);

        return response()->json($category);
    }

    public function destroy($id)
{
    $category = Category::find($id);
    if (!$category) {
        return response()->json(['error' => 'Category not found'], 404);
    }

    // Töröld a képet a storage-ből, ha létezik
    if ($category->image && Storage::disk('public')->exists($category->image)) {
        Storage::disk('public')->delete($category->image);
    }

    // Töröld a kategóriát az adatbázisból
    $category->delete();

    return response()->json(['success' => 'Category deleted successfully.']);
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