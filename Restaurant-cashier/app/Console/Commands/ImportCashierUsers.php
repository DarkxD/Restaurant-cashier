<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashierUsers;
use League\Csv\Reader;
use League\Csv\Exception;

class ImportCashierUsers extends Command
{
    protected $signature = 'import:cashier-users';
    protected $description = 'Import cashier users from CSV file';

    public function handle()
    {
        // CSV fájl elérési útja
        $csvPath = storage_path('app/private/cashier_users.csv');

        // Ellenőrizd, hogy a CSV fájl létezik-e
        if (!file_exists($csvPath)) {
            $this->error('A CSV fájl nem található: ' . $csvPath);
            return;
        }

        try {
            // CSV fájl beolvasása
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setDelimiter(';'); // Pontosvessző elválasztó
            $csv->setHeaderOffset(0); // Az első sor a fejléc

            // CSV fájl feldolgozása
            foreach ($csv as $record) {
                // Ellenőrizd, hogy a kötelező mezők léteznek-e
                if (!isset($record['pinkod']) || !isset($record['nev']) || !isset($record['jogosultsag'])) {
                    $this->warn('Hiányzó adatok a következő rekordban: ' . json_encode($record));
                    continue;
                }

                // Felhasználó létrehozása
                CashierUsers::create([
                    'pinkod' => $record['pinkod'],
                    'nev' => $record['nev'],
                    'jogosultsag' => $record['jogosultsag'],
                ]);
            }

            $this->info('Cashier users imported successfully.');
        } catch (Exception $e) {
            $this->error('Hiba történt a CSV fájl feldolgozása közben: ' . $e->getMessage());
        }
    }
}