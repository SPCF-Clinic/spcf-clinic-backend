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
        Schema::table('personal_info_field_versions', function (Blueprint $table) {
            $table->foreignId('required_with_option_id')->nullable()->constrained('personal_info_field_options')->onDelete('cascade');
        });

        Schema::table('medical_history_field_versions', function (Blueprint $table) {
            $table->foreignId('required_with_option_id')->nullable()->constrained('medical_history_field_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_info_field_versions', function (Blueprint $table) {
            $table->dropForeign(['required_with_option_id']);
            $table->dropColumn('required_with_option_id');
        });

        Schema::table('medical_history_field_versions', function (Blueprint $table) {
            $table->dropForeign(['required_with_option_id']);
            $table->dropColumn('required_with_option_id');
        });
    }
};
