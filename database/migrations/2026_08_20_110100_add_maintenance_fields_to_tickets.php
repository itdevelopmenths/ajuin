<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->foreignId('maintenance_type_id')->nullable()->after('type')
                ->constrained('maintenance_types')->nullOnDelete();
            // Snapshot tier & deadline pada saat ticket dibuat, supaya perubahan data master
            // tidak mengubah deadline ticket yang sudah berjalan.
            $table->string('maintenance_tier', 20)->nullable()->after('maintenance_type_id');
            $table->unsignedInteger('maintenance_deadline_days')->nullable()->after('maintenance_tier');
            $table->json('completion_attachments')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('maintenance_type_id');
            $table->dropColumn(['maintenance_tier', 'maintenance_deadline_days', 'completion_attachments']);
        });
    }
};
