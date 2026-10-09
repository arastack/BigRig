<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * VAT rate (percent) added to tuition, editable from Packages & VAT.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'vat_rate')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->decimal('vat_rate', 5, 2)->default(7.50);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'vat_rate')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('vat_rate');
            });
        }
    }
};
