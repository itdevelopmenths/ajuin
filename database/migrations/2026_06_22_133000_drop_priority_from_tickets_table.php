<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropIndex(['priority']);
                $table->dropColumn('priority');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            if (! Schema::hasColumn('tickets', 'priority')) {
                $table->string('priority', 20)->default('MEDIUM')->after('description')->index();
            }
        });
    }
};
