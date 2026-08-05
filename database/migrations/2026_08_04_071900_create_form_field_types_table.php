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
        Schema::create('form_field_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_answerable')->default(false);
            $table->boolean('has_options')->default(false);
            $table->boolean('can_select_multiple')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_field_types');
    }
};
