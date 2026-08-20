<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_types', function (Blueprint $table): void {
            $table->foreignId('tier_id')->nullable()->after('name')
                ->constrained('tiers')->nullOnDelete();
        });

        // Backfill: setiap kombinasi tier+deadline_days lama dijadikan satu baris Tier,
        // lalu maintenance_types diarahkan ke tier_id itu.
        $tierIdByName = [];
        foreach (DB::table('maintenance_types')->select('id', 'tier', 'deadline_days')->get() as $row) {
            if (! isset($tierIdByName[$row->tier])) {
                $tierIdByName[$row->tier] = DB::table('tiers')->insertGetId([
                    'name'          => $row->tier,
                    'deadline_days' => $row->deadline_days,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::table('maintenance_types')->where('id', $row->id)->update([
                'tier_id' => $tierIdByName[$row->tier],
            ]);
        }

        Schema::table('maintenance_types', function (Blueprint $table): void {
            $table->dropColumn(['tier', 'deadline_days']);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_types', function (Blueprint $table): void {
            $table->string('tier', 20)->nullable()->after('name');
            $table->unsignedInteger('deadline_days')->nullable();
        });

        foreach (DB::table('maintenance_types')->select('id', 'tier_id')->whereNotNull('tier_id')->get() as $row) {
            $tier = DB::table('tiers')->find($row->tier_id);
            if ($tier) {
                DB::table('maintenance_types')->where('id', $row->id)->update([
                    'tier'          => $tier->name,
                    'deadline_days' => $tier->deadline_days,
                ]);
            }
        }

        Schema::table('maintenance_types', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('tier_id');
        });
    }
};
