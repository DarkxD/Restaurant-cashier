<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Item;
use App\Models\Tag;
use League\Csv\Reader;
use League\Csv\Exception;

class ImportDemoData extends Command
{
    protected $signature = 'import:demo-data';
    protected $description = 'Import demo data from CSV files';

    public function handle()
    {
        // Ellenőrizd, hogy a CSV fájlok léteznek
        $categoriesPath = storage_path('app/public/demo_database/categories.csv');
        $itemsPath = storage_path('app/public/demo_database/items.csv');
        $tagsPath = storage_path('app/public/demo_database/tags.csv');
        $itemTagPath = storage_path('app/public/demo_database/item_tag.csv');

        if (!file_exists($categoriesPath) || !file_exists($itemsPath) || !file_exists($tagsPath) || !file_exists($itemTagPath)) {
            $this->error('Egy vagy több CSV fájl hiányzik a storage/app/public/demo_database könyvtárból.');
            return;
        }

        // Import categories
        try {
            $categoryCsv = Reader::createFromPath($categoriesPath, 'r');
            $categoryCsv->setDelimiter(';');
            $categoryCsv->setHeaderOffset(0);

            foreach ($categoryCsv as $record) {
                Category::firstOrCreate([
                    'name' => $record['name'],
                ], [
                    'description' => $record['description'],
                    'image' => $record['image'],
                    'show_cashier' => $record['show_cashier'] === '1',
                ]);
            }
            $this->info('Categories imported successfully.');
        } catch (Exception $e) {
            $this->error('Hiba történt a categories.csv feldolgozása közben: ' . $e->getMessage());
            return;
        }

        // Import items
        try {
            $itemCsv = Reader::createFromPath($itemsPath, 'r');
            $itemCsv->setDelimiter(';');
            $itemCsv->setHeaderOffset(0);

            foreach ($itemCsv as $index => $record) {
                // Ellenőrizd, hogy a category_id létezik-e
                $category = Category::find($record['category_id']);
                if (!$category) {
                    $this->warn("Nem található kategória a következő category_id-vel (sor #$index): " . $record['category_id']);
                    continue;
                }

                Item::create([
                    'category_id' => $record['category_id'],
                    'name' => $record['name'],
                    'description' => $record['description'],
                    'short_name' => $record['short_name'],
                    'image' => $record['image'],
                    'album' => json_decode($record['album'], true),
                    'show_cashier' => $record['show_cashier'] === '1',
                    'show_menu' => $record['show_menu'] === '1',
                    'price_netto' => $record['price_netto'],
                    'price_brutto' => $record['price_brutto'],
                    'default_vat' => $record['default_vat'],
                ]);
            }
            $this->info('Items imported successfully.');
        } catch (Exception $e) {
            $this->error('Hiba történt az items.csv feldolgozása közben: ' . $e->getMessage());
            return;
        }

        // Import tags
        try {
            $tagCsv = Reader::createFromPath($tagsPath, 'r');
            $tagCsv->setDelimiter(';');
            $tagCsv->setHeaderOffset(0);

            foreach ($tagCsv as $record) {
                Tag::firstOrCreate([
                    'name' => $record['name'],
                ]);
            }
            $this->info('Tags imported successfully.');
        } catch (Exception $e) {
            $this->error('Hiba történt a tags.csv feldolgozása közben: ' . $e->getMessage());
            return;
        }

        // Import item_tag relationships
        try {
            $itemTagCsv = Reader::createFromPath($itemTagPath, 'r');
            $itemTagCsv->setDelimiter(';');
            $itemTagCsv->setHeaderOffset(0);

            foreach ($itemTagCsv as $record) {
                $item = Item::find($record['item_id']);
                $tag = Tag::find($record['tag_id']);

                if (!$item || !$tag) {
                    $this->warn("Nem található item vagy tag a következő rekordhoz: item_id=" . $record['item_id'] . ", tag_id=" . $record['tag_id']);
                    continue;
                }

                $item->tags()->syncWithoutDetaching([$tag->id]);
            }
            $this->info('Item-Tag relationships imported successfully.');
        } catch (Exception $e) {
            $this->error('Hiba történt az item_tag.csv feldolgozása közben: ' . $e->getMessage());
            return;
        }

        $this->info('Demo data import completed successfully.');
    }
}