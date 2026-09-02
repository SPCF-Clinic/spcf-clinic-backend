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
        Schema::create('dispensed_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('check_in_id')->nullable()->constrained('check_ins')->onDelete('set null');
            $table->unsignedBigInteger('quantity_dispensed');
            $table->foreignId('dispensed_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('dispensed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensed_items');
    }
};
