<?php

return [
    'ticket_types' => [
        'MAINTENANCE' => 'Maintenance',
        'PEMBELIAN_PERALATAN' => 'Pembelian Peralatan (nominal di atas 1 juta)',
        'PEMBELIAN_PERLENGKAPAN' => 'Pembelian Perlengkapan',
    ],

    'statuses' => [
        'SCREENING' => 'Screening',
        'IN_PROGRESS' => 'In Progress',
        'PEMBAYARAN' => 'Pembayaran',
        'EKSEKUSI' => 'Eksekusi',
        'SELESAI' => 'Selesai',
        'REJECTED' => 'Ditolak',
    ],

    'status_flow' => [
        'SCREENING' => ['IN_PROGRESS', 'REJECTED'],
        'IN_PROGRESS' => ['PEMBAYARAN', 'REJECTED'],
        'PEMBAYARAN' => ['EKSEKUSI'],
        'EKSEKUSI' => ['SELESAI'],
        'SELESAI' => [],
        'REJECTED' => [],
    ],

    'store_categories' => [
        'TOKO' => 'Toko',
        'GUDANG' => 'Gudang',
        'MESS' => 'Mess',
    ],

    'sla_hours' => [
        'MAINTENANCE' => 48,
        'PEMBELIAN_PERALATAN' => 72,
        'PEMBELIAN_PERLENGKAPAN' => 72,
    ],

    'otp' => [
        'length'           => 6,
        'ttl_minutes'      => 10,
        'max_attempts'     => 5,
        'resend_cooldown'  => 60, // detik antar kirim ulang
    ],
];
