<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Petakan status & tipe ticket lama ke skema baru.
 *
 * Status: PENDING→SCREENING, APPROVED→EKSEKUSI, DONE→SELESAI
 *         (IN_PROGRESS & REJECTED tetap).
 * Tipe:   PENGAJUAN_BARANG→PEMBELIAN_PERALATAN, LAINNYA→PEMBELIAN_PERLENGKAPAN
 *         (MAINTENANCE tetap).
 */
return new class extends Migration
{
    private array $statusMap = [
        'PENDING'  => 'SCREENING',
        'APPROVED' => 'EKSEKUSI',
        'DONE'     => 'SELESAI',
    ];

    private array $typeMap = [
        'PENGAJUAN_BARANG' => 'PEMBELIAN_PERALATAN',
        'LAINNYA'          => 'PEMBELIAN_PERLENGKAPAN',
    ];

    public function up(): void
    {
        foreach ($this->statusMap as $from => $to) {
            DB::table('tickets')->where('status', $from)->update(['status' => $to]);
            DB::table('ticket_logs')->where('from_status', $from)->update(['from_status' => $to]);
            DB::table('ticket_logs')->where('to_status', $from)->update(['to_status' => $to]);
        }

        foreach ($this->typeMap as $from => $to) {
            DB::table('tickets')->where('type', $from)->update(['type' => $to]);
        }
    }

    public function down(): void
    {
        foreach ($this->statusMap as $from => $to) {
            DB::table('tickets')->where('status', $to)->update(['status' => $from]);
            DB::table('ticket_logs')->where('from_status', $to)->update(['from_status' => $from]);
            DB::table('ticket_logs')->where('to_status', $to)->update(['to_status' => $from]);
        }

        foreach ($this->typeMap as $from => $to) {
            DB::table('tickets')->where('type', $to)->update(['type' => $from]);
        }
    }
};
