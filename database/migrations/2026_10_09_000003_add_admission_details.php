<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Details the course requirements ask for (age, NIN, licence) and who to call in an emergency.
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('address')->nullable()->after('mobileNumber');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 50)->nullable()->after('city');
            $table->string('nin', 20)->nullable()->after('state');
            $table->string('drivers_licence_number', 50)->nullable()->after('nin');
            $table->date('drivers_licence_expiry')->nullable()->after('drivers_licence_number');
            $table->string('next_of_kin_name')->nullable()->after('drivers_licence_expiry');
            $table->string('next_of_kin_phone', 30)->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_relationship', 50)->nullable()->after('next_of_kin_phone');
        });

        Schema::table('medicals', function (Blueprint $table) {
            $table->timestamp('consent_given_at')->nullable()->after('attachments');
        });

        Schema::create('applicant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_documents');

        Schema::table('medicals', fn (Blueprint $table) => $table->dropColumn('consent_given_at'));

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'address', 'city', 'state', 'nin', 'drivers_licence_number',
                'drivers_licence_expiry', 'next_of_kin_name', 'next_of_kin_phone', 'next_of_kin_relationship',
            ]);
        });
    }
};
