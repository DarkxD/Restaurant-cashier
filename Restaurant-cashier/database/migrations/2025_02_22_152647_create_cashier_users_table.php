<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_users', function (Blueprint $table) {
            $table->id();
            $table->string('pinkod',255)->unique(); // Pinkód tárolása
            $table->string('nev'); // Név tárolása
            $table->string('jogosultsag'); // Jogosultság tárolása (pl. 'admin', 'user')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_users');
    }
};
