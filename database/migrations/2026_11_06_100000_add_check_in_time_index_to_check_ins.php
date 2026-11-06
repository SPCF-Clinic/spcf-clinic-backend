<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The check-in listings now ORDER BY check_in_time DESC, id DESC, so the
 * newest page can be read straight off the index instead of sorting the
 * whole (and ever-growing) table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->index('check_in_time', 'check_ins_check_in_time_index', 'btree');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropIndex('check_ins_check_in_time_index');
        });
    }
};
