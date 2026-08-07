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
            $table->string('name');
            $table->enum('type', ['Medicine', 'Supply']);
            $table->enum('category', ['Pain Reliever', 'Antibiotic', 'Cough & Cold', 'Antihistamine', 'Wound Care', 'PPE']);
            $table->enum('unit', ['Tablets', 'Boxes', 'Bottles', 'Packs', 'Pairs', 'Rolls']);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
