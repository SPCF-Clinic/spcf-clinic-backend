<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the tables that grow with clinic traffic.
 *
 * Only filters/sorts that the repositories actually run are covered. All indexes
 * are B-tree (InnoDB's only general-purpose type); the algorithm is passed
 * explicitly so the intent is visible.
 *
 * Not indexed on purpose:
 *  - FK columns (user_id, item_id, check_in_id, ...) — MySQL already creates
 *    a B-tree index for every constrained() foreign key.
 *  - LIKE '%term%' columns (reason_for_visit, items.name, names, username) —
 *    a leading wildcard can't use a B-tree.
 *  - Small/static tables (items, beds, form fields, departments, courses).
 *  - activity_logs.group — only used as NOT IN ('INVENTORY'), which matches
 *    most rows, so an index would never be chosen.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Dashboard (status = 'Checked In', every load) and CheckInEnumsRepository
        // (status = 'Checked In' AND current_bed_id IS NULL). 'Checked In' is a tiny
        // slice of an ever-growing table, so this stays highly selective.
        Schema::table('check_ins', function (Blueprint $table) {
            $table->index(['status', 'current_bed_id'], 'check_ins_status_current_bed_index', 'btree');
        });

        // IndexDispensedItemRepository: ORDER BY created_at DESC over the whole table.
        Schema::table('dispensed_items', function (Blueprint $table) {
            $table->index('created_at', 'dispensed_items_created_at_index', 'btree');
        });

        // IndexActivityLogRepository: ORDER BY created_at DESC (cursor pagination)
        // plus the optional single-day filter.
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_index', 'btree');
        });

        // EAV table. NameSearch self-joins it three times on (user_id, personal_info_field_id)
        // for every name search (users, check-ins, dispensed items); ShowStudent and the
        // update requests look up by the same pair. This replaces the implicit user_id
        // FK index; the personal_info_field_id FK index is kept for the field-id scan.
        Schema::table('user_personal_infos', function (Blueprint $table) {
            $table->index(['user_id', 'personal_info_field_id'], 'user_personal_infos_user_field_index', 'btree');
        });
    }

    public function down(): void
    {
        Schema::table('user_personal_infos', function (Blueprint $table) {
            // The composite may be what backs the user_id foreign key (MySQL drops the
            // implicit FK index once a usable one exists), so restore a plain one first.
            $table->index('user_id');
            $table->dropIndex('user_personal_infos_user_field_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
        });

        Schema::table('dispensed_items', function (Blueprint $table) {
            $table->dropIndex('dispensed_items_created_at_index');
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropIndex('check_ins_status_current_bed_index');
        });
    }
};
