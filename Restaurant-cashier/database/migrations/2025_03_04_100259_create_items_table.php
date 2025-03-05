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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('short_name')->nullable();
            $table->string('image')->nullable();
            $table->json('album')->nullable();
            $table->boolean('show_cashier')->default(1);
            $table->boolean('show_menu')->default(0);
            $table->unsignedInteger('price_netto')->nullable();
            $table->unsignedInteger('price_brutto');
            $table->unsignedInteger('default_vat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::dropIfExists('items');
    }
};
