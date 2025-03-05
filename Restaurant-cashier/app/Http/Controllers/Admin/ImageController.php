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
            // Eltávolítjuk a felesleges escape karaktereket és idézőjeleket
            $album = trim($album, '"'); // Eltávolítjuk a külső idézőjeleket
            $album = str_replace('\"', '"', $album); // Escape karakterek eltávolítása
            // JSON dekódolás
            $decoded = json_decode($album, true);
            // Ellenőrizzük, hogy a dekódolás sikeres volt-e, és az eredmény egy tömb
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return [];
            }
            // Fájlnevek kinyerése és az 'images/' és 'images\/' előtagok eltávolítása
            return array_map(function ($path) {
                return str_replace(['images/', 'images\/'], '', $path); // Eltávolítjuk az előtagokat
            }, $decoded);
        })->flatten()->filter()->unique(); // Egyedivé tesszük a fájlneveket

        // Összefésüljük az összes fájlnevet
        $databaseImages = $categoryImages->merge($itemImages)
            ->merge($cleanedAlbumImages) // Album képek hozzáadása
            ->filter()
            ->map(function ($image) {
                return str_replace(['images/', 'images\/'], '', $image); // Eltávolítjuk az előtagokat
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

        // Táblázat generálása törlés gombokkal
        echo '<table border="1">';
        echo '<tr><th>Kép</th><th>Fájlnév</th><th>Létezik az adatbázisban?</th><th>Műveletek</th></tr>';
        foreach ($comparison as $row) {
            echo '<tr>';
            echo '<td><img src="' . asset('storage/images/' . $row['filename']) . '" width="100"></td>';
            echo '<td>' . $row['filename'] . '</td>';
            echo '<td>' . $row['exists_in_database'] . '</td>';
            echo '<td>';
            if ($row['exists_in_database'] === 'Nem') {
                echo '<form action="' . route('delete.image', ['filename' => $row['filename']]) . '" method="POST" style="display:inline;">';
                echo csrf_field();
                echo method_field('DELETE');
                echo '<button type="submit" onclick="return confirm(\'Biztosan törölni szeretnéd ezt a képet?\')">Törlés</button>';
                echo '</form>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function deleteImage($filename)
    {
        // Ellenőrizzük, hogy a fájl létezik-e a tárolóban
        if (Storage::disk('public')->exists('images/' . $filename)) {
            // Töröljük a fájlt
            Storage::disk('public')->delete('images/' . $filename);
            return redirect()->back()->with('success', 'A kép sikeresen törölve!');
        }

        // Ha a fájl nem létezik, hibaüzenetet küldünk vissza
        return redirect()->back()->with('error', 'A kép nem található!');
    }
}