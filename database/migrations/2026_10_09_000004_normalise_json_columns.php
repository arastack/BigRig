<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Older code JSON-encoded these columns twice ("\"[4]\""). Store plain JSON arrays ([4]).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([['subscriptions', 'objectives'], ['training_schedules', 'students']] as [$table, $column]) {
            DB::table($table)->select('id', $column)->orderBy('id')->each(function ($row) use ($table, $column) {
                $ids = json_decode($row->{$column} ?? '[]', true);
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                $ids = array_values(array_map('intval', array_filter((array) $ids, 'is_numeric')));

                DB::table($table)->where('id', $row->id)->update([$column => json_encode($ids)]);
            });
        }
    }

    public function down(): void
    {
        // Plain JSON is still readable by every accessor; nothing to undo.
    }
};
