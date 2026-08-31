<?php

return [
    'ticket_types' => [
        'MAINTENANCE' => 'Maintenance',
        'PEMBELIAN_PERALATAN' => 'Pembelian Peralatan (nominal di atas 500 ribu)',
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
    ],

    'permission_group_labels' => [
        'ticket' => 'Ticket',
        'report' => 'Laporan',
        'user' => 'User',
        'store' => 'Toko',
        'maintenance_type' => 'Jenis Maintenance',
        'role' => 'Role',
        'scope' => 'Scope Akses',
    ],

    'permission_labels' => [
        'ticket.view' => 'Lihat ticket',
        'ticket.create' => 'Buat ticket',
        'ticket.update_status' => 'Update status ticket',
        'ticket.delete' => 'Hapus ticket',
        'ticket.export' => 'Export ticket',
        'report.view' => 'Lihat laporan',
        'report.export' => 'Export laporan',
        'user.view' => 'Lihat user',
        'user.create' => 'Tambah user',
        'user.edit' => 'Edit user',
        'user.delete' => 'Hapus user',
        'store.view' => 'Lihat toko',
        'store.manage' => 'Kelola toko',
        'maintenance_type.view' => 'Lihat jenis maintenance',
        'maintenance_type.manage' => 'Kelola jenis maintenance',
        'role.view' => 'Lihat role',
        'role.manage' => 'Kelola role',
        'scope.manage' => 'Kelola scope akses',
    ],
];
