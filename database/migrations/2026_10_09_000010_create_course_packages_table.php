<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Course packages (combo discounts), previously hard-coded in config/admissions.php.
 * course_groups is an array of arrays of training_objectives ids: a package applies
 * when the selection includes at least one course from every group.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->unsignedSmallInteger('weeks')->nullable();
            $table->json('course_groups');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the existing CDL + Forklift combo
        $idsLike = fn (string $word) => DB::table('training_objectives')
            ->where('objective', 'like', "%{$word}%")->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();

        $cdl = $idsLike('CDL');
        $forklift = $idsLike('Forklift');

        if ($cdl && $forklift) {
            DB::table('course_packages')->insert([
                'name' => 'CDL + Forklift Combo',
                'description' => 'Enrol for truck training (Standard or Automatic) together with Forklift Training and receive a ₦50,000 discount.',
                'discount' => 50000,
                'weeks' => 14,
                'course_groups' => json_encode([$cdl, $forklift]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_packages');
    }
};
