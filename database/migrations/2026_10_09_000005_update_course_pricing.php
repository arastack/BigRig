<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Academy fee schedule (October 2026):
 *   CDL – Standard Transmission  12 weeks  ₦2,200,000
 *   CDL – Automatic Transmission 12 weeks  ₦2,000,000
 *   Forklift Training             4 weeks   ₦550,000
 *   CDL + Forklift combo          ₦50,000 discount (see config/admissions.php)
 *   Conventional Car Driving      4 weeks   ₦350,000
 *
 * Existing applications keep the amount they were quoted.
 */
return new class extends Migration
{
    private const CDL = 'CDL Commercial Vehicle Training';
    private const CDL_STANDARD = 'CDL Commercial Vehicle Training - Standard Transmission';
    private const CDL_AUTOMATIC = 'CDL Commercial Vehicle Training - Automatic Transmission';

    public function up(): void
    {
        if (! Schema::hasColumn('subscriptions', 'discount')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            });
        }

        $now = now();
        $cdl = DB::table('training_objectives')->whereIn('objective', [self::CDL, self::CDL_STANDARD])->first();

        if ($cdl) {
            DB::table('training_objectives')->where('id', $cdl->id)->update([
                'objective' => self::CDL_STANDARD, 'price' => 2200000, 'duration' => 12, 'updated_at' => $now,
            ]);

            // The automatic-transmission course shares the CDL content, curriculum and instructors
            if (! DB::table('training_objectives')->where('objective', self::CDL_AUTOMATIC)->exists()) {
                $copy = (array) $cdl;
                unset($copy['id']);
                $automaticId = DB::table('training_objectives')->insertGetId(array_merge($copy, [
                    'objective' => self::CDL_AUTOMATIC, 'price' => 2000000, 'duration' => 12, 'created_at' => $now, 'updated_at' => $now,
                ]));

                foreach (DB::table('curriculum')->where('objective_id', $cdl->id)->orderBy('id')->get() as $topic) {
                    $row = (array) $topic;
                    unset($row['id']);
                    DB::table('curriculum')->insert(array_merge($row, ['objective_id' => $automaticId, 'created_at' => $now, 'updated_at' => $now]));
                }

                foreach (DB::table('role_courses')->where('course_id', $cdl->id)->pluck('role_id') as $roleId) {
                    DB::table('role_courses')->insertOrIgnore(['role_id' => $roleId, 'course_id' => $automaticId, 'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }

        DB::table('training_objectives')->where('objective', 'like', 'Forklift%')->update(['price' => 550000, 'duration' => 4, 'updated_at' => $now]);
        DB::table('training_objectives')->where('objective', 'like', '%Car Driving%')->update(['price' => 350000, 'duration' => 4, 'updated_at' => $now]);
    }

    public function down(): void
    {
        DB::table('training_objectives')->where('objective', self::CDL_STANDARD)->update(['objective' => self::CDL, 'price' => 1800000]);
        DB::table('training_objectives')->where('objective', 'like', 'Forklift%')->update(['price' => 470000, 'duration' => 3]);
        DB::table('training_objectives')->where('objective', 'like', '%Car Driving%')->update(['duration' => 6]);
        // The automatic course is left in place: applications may already reference it.

        if (Schema::hasColumn('subscriptions', 'discount')) {
            Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn('discount'));
        }
    }
};
