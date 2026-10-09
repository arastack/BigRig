<?php

use App\Support\AdmissionSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admissions settings edited in the admin panel, seeded with the wording the
     * application form launched with. Also lets the web health answers be optional
     * (an admin can un-require a question), so those columns become nullable.
     */
    public function up(): void
    {
        Schema::create('admission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        foreach (AdmissionSettings::defaults() as $key => $value) {
            DB::table('admission_settings')->insert(['key' => $key, 'value' => json_encode($value), 'created_at' => $now, 'updated_at' => $now]);
        }

        Schema::table('medicals', function (Blueprint $table) {
            $table->string('weed')->nullable()->default(null)->change();
            $table->string('alcohol')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_settings');
    }
};
