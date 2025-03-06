<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function compareImages()
    {
        // Adatbázisból lekérdezzük a képek elérési útjait
        $categoryImages = DB::table('categories')->pluck('image');
        $itemImages = DB::table('items')->pluck('image');
        $albumImages = DB::table('items')->pluck('album');

        // Tisztítjuk az album mezőben található JSON adatokat
        $cleanedAlbumImages = $albumImages->map(function ($album) {
            if (empty($album) || $album === '[]' || $album === '""') {
                return [];
            }
            $album = trim($album, '"');
            $album = str_replace('\"', '"', $album);
            $decoded = json_decode($album, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return [];
            }
            return array_map(function ($path) {
                return str_replace(['images/', 'images\/'], '', $path);
            }, $decoded);
        })->flatten()->filter()->unique();

        // Összefésüljük az összes fájlnevet
        $databaseImages = $categoryImages->merge($itemImages)
            ->merge($cleanedAlbumImages)
            ->filter()
            ->map(function ($image) {
                return str_replace(['images/', 'images\/'], '', $image);
            })
            ->unique();

        // Lekérdezzük a tárolóban lévő összes fájlt
        $storageImages = collect(Storage::disk('public')->files('images'))->map(function ($file) {
            return basename($file);
        });

        // Összehasonlítjuk a két listát
        $comparison = $storageImages->map(function ($image) use ($databaseImages) {
            return [
                'filename' => $image,
                'exists_in_database' => $databaseImages->contains($image) ? 'Igen' : 'Nem',
            ];
        });

        // Adatok átadása a view-nak
        return view('admin.image_comparison', compact('comparison'));
    }

    public function deleteImage($filename)
    {
        if (Storage::disk('public')->exists('images/' . $filename)) {
            Storage::disk('public')->delete('images/' . $filename);
            return redirect()->back()->with('success', 'A kép sikeresen törölve!');
        }

        return redirect()->back()->with('error', 'A kép nem található!');
    }
}