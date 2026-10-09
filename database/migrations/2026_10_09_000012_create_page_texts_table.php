<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Wording the school has saved for the public website's editable text blocks
     * (see config/page-text.php). Missing keys fall back to the catalogue default.
     */
    public function up(): void
    {
        Schema::create('page_texts', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_texts');
    }
};
