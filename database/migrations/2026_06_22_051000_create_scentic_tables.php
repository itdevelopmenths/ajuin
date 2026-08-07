<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->uuid('public_token')->default(DB::raw('(gen_random_uuid())'))->unique();
            $table->timestamps();

            $table->index(['province_id', 'code']);
        });

        Schema::create('store_user', function (Blueprint $table): void {
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['store_id', 'user_id']);
        });

        Schema::create('user_scopes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('scope_type', 20)->index();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'scope_type']);
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number', 32)->unique();
            $table->string('submitted_by', 100);
            $table->string('type', 40)->index();
            $table->string('source', 40)->index();
            $table->text('description');
            $table->string('status', 30)->default('PENDING')->index();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('attachment_path')->nullable();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'status', 'created_at']);
        });

        Schema::create('ticket_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_logs');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('user_scopes');
        Schema::dropIfExists('store_user');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('provinces');
    }
};
