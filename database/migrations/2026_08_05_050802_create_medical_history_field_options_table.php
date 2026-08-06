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
        Schema::create('medical_history_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_version_id')->constrained('medical_history_field_versions')->onDelete('cascade');
            $table->string('option_value');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_history_field_options');
    }
};
