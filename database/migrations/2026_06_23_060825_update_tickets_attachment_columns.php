<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('handled_by');
        });

        // Migrate existing attachment_path to attachments array
        DB::table('tickets')->whereNotNull('attachment_path')->orderBy('id')->chunk(100, function ($tickets) {
            foreach ($tickets as $ticket) {
                DB::table('tickets')->where('id', $ticket->id)->update([
                    'attachments' => json_encode([$ticket->attachment_path])
                ]);
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('handled_by');
        });

        // Revert first attachment back to attachment_path
        DB::table('tickets')->whereNotNull('attachments')->orderBy('id')->chunk(100, function ($tickets) {
            foreach ($tickets as $ticket) {
                $atts = json_decode($ticket->attachments, true);
                if (!empty($atts)) {
                    DB::table('tickets')->where('id', $ticket->id)->update([
                        'attachment_path' => $atts[0]
                    ]);
                }
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
