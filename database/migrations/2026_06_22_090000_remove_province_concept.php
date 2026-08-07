<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 * Hapus konsep provinsi dari sistem.
 * SPV langsung membawahi Toko (STORE scope) tanpa hierarki provinsi.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus baris user_scope yang bertipe PROVINCE — raw SQL menghindari false positive IDE
        DB::statement("DELETE FROM user_scopes WHERE scope_type = 'PROVINCE'");

        // 2. Drop kolom province_id dari user_scopes
        Schema::table('user_scopes', function (Blueprint $table): void {
            $table->dropForeign(['province_id']);
            $table->dropColumn('province_id');
        });

        // 3. Drop kolom province_id dari stores
        Schema::table('stores', function (Blueprint $table): void {
            $table->dropIndex(['province_id', 'code']);
            $table->dropForeign(['province_id']);
            $table->dropColumn('province_id');
            // Tambah index ulang tanpa province_id
            $table->index('code');
        });

        // 4. Drop tabel provinces (cascade sudah handled di step 3)
        Schema::dropIfExists('provinces');
    }

    public function down(): void
    {
        // Recreate provinces
        Schema::create('provinces', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // Restore province_id ke stores
        Schema::table('stores', function (Blueprint $table): void {
            $table->dropIndex(['code']);
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->index(['province_id', 'code']);
        });

        // Restore province_id ke user_scopes
        Schema::table('user_scopes', function (Blueprint $table): void {
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
