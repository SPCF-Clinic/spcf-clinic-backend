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
        Schema::create('item_contents', function (Blueprint $table) {
            $table->id();
            $table->morphs('parent');
            $table->enum('content_unit', ['ml', 'Tablets', 'pcs', 'Bottles', 'Packs', 'Pairs', 'Rolls']);
            $table->unsignedBigInteger('quantity_per_item_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_contents');
    }
};
