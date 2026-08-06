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
        Schema::create('personal_info_field_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_info_field_id')->constrained('personal_info_fields')->onDelete('cascade');
            $table->integer('version_number');
            $table->string('field_name');
            $table->foreignId('form_field_type_id')->constrained('form_field_types')->onDelete('cascade');
            $table->boolean('is_required')->default(false);
            $table->foreignId('required_with_field_id')->nullable()->constrained('personal_info_fields')->onDelete('cascade');
            $table->string('required_with_field_value')->nullable();
            $table->integer('form_order')->nullable();
            $table->text('description_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_info_field_versions');
    }
};
