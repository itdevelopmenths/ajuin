<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ajuin' }} — Heaven Scent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { font-family: 'Inter', system-ui, sans-serif; }

        :root {
            --c-primary: #6366f1;
            --c-primary-dark: #4f46e5;
            --c-secondary: #8b5cf6;
            --c-ink: #0f172a;
            --c-muted: #64748b;
            --c-line: #e2e8f0;
            --c-surface: #f8fafc;
            --sidebar-w: 268px;
        }

        /* ─── Body background ─────────────────────────── */
        body {
            background: linear-gradient(150deg, #f0f4ff 0%, #f8fafc 40%, #eef2fb 100%);
            min-height: 100vh;
            color: var(--c-ink);
        }

        /* ─── Sidebar ─────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            z-index: 50;
            border-right: 1px solid rgba(226,232,240,0.8);
            box-shadow: 4px 0 24px rgba(15,23,42,0.03);
        }

        .sidebar-brand {
            padding: 1.375rem 1.125rem 1.125rem;
            display: flex;
            align-items: center;
            gap: .875rem;
            border-bottom: 1px solid rgba(226,232,240,0.8);
            text-decoration: none;
        }

        .sidebar-logo {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-secondary) 100%);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1rem;
            font-weight: 900;
            color: #fff;
            box-shadow: 0 4px 14px rgba(99,102,241,0.45);
            letter-spacing: -.02em;
        }

        .sidebar-brand-text { line-height: 1.1; }
        .sidebar-brand-name { font-size: 1.0625rem; font-weight: 800; color: #0f172a; }
        .sidebar-brand-sub  { font-size: .6875rem; font-weight: 600; color: #64748b; margin-top: 2px; }

        .sidebar-nav {
            flex: 1;
            padding: .875rem .75rem;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .sidebar-section {
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 1.125rem .5rem .4rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .6875rem;
            padding: .5625rem .75rem;
            border-radius: .625rem;
            color: #475569;
            font-size: .8125rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .17s ease, color .17s ease, transform .17s ease;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-link .nav-icon {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            opacity: .7;
            transition: opacity .17s;
        }

        .nav-link:hover {
            background: rgba(241,245,249,0.8);
            color: #0f172a;
            transform: translateX(3px);
        }
        .nav-link:hover .nav-icon { opacity: 1; }

        .nav-link.active {
            background: linear-gradient(135deg,rgba(99,102,241,0.08),rgba(139,92,246,0.05));
            color: #4f46e5;
            border: 1px solid rgba(99,102,241,0.15);
        }
        .nav-link.active .nav-icon { opacity: 1; color: #6366f1; }

        .sidebar-footer {
            padding: .875rem 1rem;
            border-top: 1px solid rgba(226,232,240,0.8);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: .625rem;
        }

        .sidebar-avatar {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--c-primary), var(--c-secondary));
            display: grid;
            place-items: center;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
        }

        .sidebar-user-name  { font-size: .8rem; font-weight: 700; color: #0f172a; line-height: 1.2; }
        .sidebar-user-role  { font-size: .6875rem; font-weight: 500; color: #64748b; }

        /* ─── Main wrapper ────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            min-height: 100vh;
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-width: 0;
        }

        /* ─── Topbar ──────────────────────────────────── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255,255,255,0.84);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(226,232,240,0.7);
            padding: .6875rem 1.5rem;
        }

        /* ─── Cards ───────────────────────────────────── */
        .card {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226,232,240,0.65);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 8px 24px rgba(15,23,42,0.055);
        }

        /* Alias agar view lama tetap bekerja */
        .content-card {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226,232,240,0.65);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 8px 24px rgba(15,23,42,0.055);
        }

        .card-lift {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 24px rgba(15,23,42,0.09);
        }

        /* ─── Stat card icon blobs ────────────────────── */
        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: grid;
            place-items: center;
        }
        .stat-icon svg { width: 20px; height: 20px; }

        .si-indigo { background: linear-gradient(135deg,#6366f1,#818cf8); color:#fff; box-shadow:0 4px 12px rgba(99,102,241,0.3); }
        .si-violet { background: linear-gradient(135deg,#8b5cf6,#a78bfa); color:#fff; box-shadow:0 4px 12px rgba(139,92,246,0.3); }
        .si-amber  { background: linear-gradient(135deg,#f59e0b,#fbbf24); color:#fff; box-shadow:0 4px 12px rgba(245,158,11,0.3); }
        .si-blue   { background: linear-gradient(135deg,#3b82f6,#60a5fa); color:#fff; box-shadow:0 4px 12px rgba(59,130,246,0.3); }
        .si-green  { background: linear-gradient(135deg,#10b981,#34d399); color:#fff; box-shadow:0 4px 12px rgba(16,185,129,0.3); }
        .si-red    { background: linear-gradient(135deg,#ef4444,#f87171); color:#fff; box-shadow:0 4px 12px rgba(239,68,68,0.3); }
        .si-teal   { background: linear-gradient(135deg,#14b8a6,#2dd4bf); color:#fff; box-shadow:0 4px 12px rgba(20,184,166,0.3); }
        .si-slate  { background: linear-gradient(135deg,#64748b,#94a3b8); color:#fff; box-shadow:0 4px 12px rgba(100,116,139,0.3); }

        /* ─── Buttons ─────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4375rem;
            padding: .5875rem 1.125rem;
            border-radius: .625rem;
            font-size: .8125rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .18s ease;
            line-height: 1;
            white-space: nowrap;
        }

        .btn svg { width: 15px; height: 15px; }

        .btn-primary {
            background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-secondary) 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(99,102,241,0.32);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 18px rgba(99,102,241,0.45);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1.5px solid #e5e7eb;
        }
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fecaca;
        }
        .btn-danger:hover { background: #fee2e2; }

        .btn-ghost {
            background: transparent;
            color: var(--c-muted);
            padding: .5rem .75rem;
        }
        .btn-ghost:hover { background: rgba(0,0,0,0.05); color: var(--c-ink); }

        /* Keep legacy aliases */
        .btn-primary:not(.btn) {
            display:inline-flex;align-items:center;gap:.4rem;padding:.5875rem 1.125rem;
            border-radius:.625rem;font-size:.8125rem;font-weight:600;cursor:pointer;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;
            box-shadow:0 2px 8px rgba(99,102,241,.32);transition:all .18s;text-decoration:none;
        }
        .btn-muted {
            display:inline-flex;align-items:center;gap:.4rem;padding:.5625rem 1rem;
            border-radius:.625rem;font-size:.8125rem;font-weight:600;cursor:pointer;
            background:#fff;color:#374151;border:1.5px solid #e5e7eb;
            transition:all .18s;text-decoration:none;
        }
        .btn-muted:hover { background:#f9fafb; transform:translateY(-1px); }

        /* ─── Form ────────────────────────────────────── */
        .select2-container--default .select2-selection--single {
            border: 1.5px solid #e5e7eb !important;
            border-radius: .625rem !important;
            height: auto !important;
            padding: .5rem .875rem !important;
            font-size: .875rem !important;
            color: var(--c-ink) !important;
            outline: none !important;
            transition: border-color .15s ease, box-shadow .15s ease !important;
            background-color: #fff !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: var(--c-primary) !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: normal !important;
            color: var(--c-ink) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: .875rem !important;
            height: auto !important;
        }
        .select2-dropdown {
            border: 1.5px solid var(--c-primary) !important;
            border-radius: .625rem !important;
            box-shadow: 0 4px 24px rgba(15,23,42,0.1) !important;
            font-size: .875rem !important;
            overflow: hidden !important;
            z-index: 9999 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #e5e7eb !important;
            border-radius: .5rem !important;
            padding: .4rem .6rem !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--c-primary) !important;
            outline: none !important;
        }
        .select2-results__option {
            padding: .5rem .875rem !important;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--c-primary) !important;
            color: white !important;
        }

        .form-label {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .3125rem;
        }

        .form-input {
            display: block;
            width: 100%;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: .625rem;
            padding: .5875rem .875rem;
            font-size: .875rem;
            color: var(--c-ink);
            outline: none;
            font-family: inherit;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .form-input:focus {
            border-color: var(--c-primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        textarea.form-input { resize: vertical; }

        /* ─── Badges ──────────────────────────────────── */
        .badge, .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .3125rem;
            border-radius: 999px;
            padding: .225rem .6875rem;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .badge::before, .status-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .badge-SCREENING                  { background:#fef9c3; color:#854d0e; }
        .badge-IN_PROGRESS                { background:#dbeafe; color:#1e40af; }
        .badge-PEMBAYARAN                 { background:#ffedd5; color:#9a3412; }
        .badge-EKSEKUSI                   { background:#ede9fe; color:#5b21b6; }
        .badge-SELESAI                    { background:#d1fae5; color:#065f46; }
        .badge-REJECTED                   { background:#fee2e2; color:#b91c1c; }
        /* legacy status aliases (data lama sebelum migrasi) */
        .badge-PENDING                    { background:#fef9c3; color:#854d0e; }
        .badge-APPROVED                   { background:#dcfce7; color:#15803d; }
        .badge-DONE                       { background:#d1fae5; color:#065f46; }
        /* store category badges */
        .badge-cat-TOKO                   { background:#e0e7ff; color:#3730a3; }
        .badge-cat-GUDANG                 { background:#fef3c7; color:#92400e; }
        .badge-cat-MESS                   { background:#ccfbf1; color:#115e59; }
        /* ─── Alert / Flash ───────────────────────────── */
        .alert {
            border-radius: .75rem;
            padding: .8125rem 1rem;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: .625rem;
        }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
        .alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }

        /* ─── Page header helper ──────────────────────── */
        .eyebrow {
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--c-primary), var(--c-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-title {
            font-size: 1.625rem;
            font-weight: 800;
            letter-spacing: -.025em;
            color: var(--c-ink);
            line-height: 1.15;
        }

        /* ─── DataTable overrides ─────────────────────── */
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }

        table.dataTable thead th {
            background: #f8fafc;
            padding: .75rem 1rem !important;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .055em;
            text-transform: uppercase;
            color: var(--c-muted);
            border-bottom: 1px solid var(--c-line) !important;
            border-top: none !important;
        }

        table.dataTable tbody td {
            padding: .9rem 1rem !important;
            border-bottom: 1px solid #f1f5f9;
            font-size: .8125rem;
            color: #1e293b;
            vertical-align: middle;
        }

        table.dataTable tbody tr:hover td { background: #fafbff; }

        .dt-container { font-family: 'Inter', sans-serif; }
        .dt-container .dt-search label,
        .dt-container .dt-length label { font-size: .8rem; color: var(--c-muted); }
        .dt-container .dt-input,
        .dt-container .dt-select {
            border: 1.5px solid #e5e7eb !important;
            border-radius: .5rem !important;
            padding: .375rem .625rem !important;
            font-size: .8rem !important;
            font-family: 'Inter', sans-serif !important;
            outline: none !important;
        }
        .dt-container .dt-input:focus,
        .dt-container .dt-select:focus {
            border-color: var(--c-primary) !important;
            box-shadow: 0 0 0 2px rgba(99,102,241,.12) !important;
        }
        .dt-pager .dt-pager-button {
            border-radius: .4375rem !important;
            font-size: .8rem !important;
        }
        .dt-pager .dt-pager-button.current {
            background: var(--c-primary) !important;
            color: #fff !important;
            border: none !important;
        }
        .dt-processing {
            background: rgba(255,255,255,.94) !important;
            border-radius: .75rem !important;
            box-shadow: 0 4px 24px rgba(15,23,42,0.1) !important;
            font-size: .875rem !important;
            color: var(--c-primary) !important;
        }
        .dt-info { font-size: .8rem; color: var(--c-muted); }

        /* ─── Timeline ────────────────────────────────── */
        .timeline { list-style: none; padding: 0; margin: 0; position: relative; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: linear-gradient(180deg, var(--c-primary), var(--c-secondary));
            opacity: .2;
            border-radius: 2px;
        }
        .timeline-item {
            display: flex;
            gap: 1rem;
            padding-bottom: 1.25rem;
            position: relative;
        }
        .timeline-dot {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg,var(--c-primary),var(--c-secondary));
            display: grid;
            place-items: center;
            margin-top: 2px;
            box-shadow: 0 2px 8px rgba(99,102,241,.3);
        }
        .timeline-dot svg { width: 15px; height: 15px; color: #fff; }



        /* ─── Divider ─────────────────────────────────── */
        .section-divider { border: none; border-top: 1px solid var(--c-line); margin: 0; }
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15,23,42,0.4);
                backdrop-filter: blur(4px);
                z-index: 45;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .sidebar-backdrop.show {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>
<body class="antialiased">

@auth
<div class="lg:flex" style="min-height:100vh">

    {{-- ── Sidebar ────────────────────────────────── --}}
    <aside class="sidebar">
        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <span class="sidebar-logo">A</span>
            <span class="sidebar-brand-text">
                <span class="sidebar-brand-name">Ajuin</span>
                <span class="sidebar-brand-sub">Heaven Scent Ops</span>
            </span>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                </svg>
                Dashboard
            </a>

            @can('ticket.view')
            <a href="{{ route('tickets.index') }}"
               class="nav-link {{ request()->routeIs('tickets.index','tickets.show') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>
                </svg>
                Tickets
            </a>
            @endcan

            @can('ticket.create')
            <a href="{{ route('tickets.create') }}"
               class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                Buat Ticket
            </a>
            @endcan

            @can('report.view')
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                </svg>
                Laporan
            </a>
            @endcan

            @canany(['role.manage','user.view','store.manage'])
            <div class="sidebar-section">Admin</div>

            @can('role.manage')
            <a href="{{ route('admin.roles.index') }}"
               class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                </svg>
                Roles
            </a>
            @endcan

            @can('user.view')
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
                Users
            </a>
            @endcan

            @can('store.manage')
            <a href="{{ route('admin.stores.index') }}"
               class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
                </svg>
                Lokasi
            </a>
            @endcan
            @endcanany
        </nav>

        {{-- User footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <span class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                <div style="flex:1;min-width:0">
                    <div class="sidebar-user-name truncate">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->getRoleNames()->join(', ') ?: 'User' }}</div>
                </div>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" style="background:none;border:none;cursor:pointer;padding:4px;border-radius:6px;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='none'">
                        <svg style="width:17px;height:17px;color:rgba(148,163,184,.7)" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main content ────────────────────────────── --}}
    <div class="main-wrapper">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden text-slate-500 hover:text-indigo-600 transition" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                        <svg style="width:24px;height:24px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color:var(--c-muted)">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                        <p class="text-sm font-semibold mt-0.5" style="color:var(--c-ink)">{{ $title ?? 'Ajuin' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="hidden sm:flex items-center gap-2 text-sm">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-xs font-bold text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                        <span class="font-medium text-slate-700 text-xs">{{ auth()->user()->name }}</span>
                    </span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="mx-auto w-full max-w-7xl px-5 py-6 lg:px-7 flex-1">
            @if(session('status'))
                <div class="alert alert-success mb-5">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error mb-5">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    
    <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            
            sidebar.classList.toggle('sidebar-open');
            if (sidebar.classList.contains('sidebar-open')) {
                backdrop.style.display = 'block';
                setTimeout(() => backdrop.classList.add('show'), 10);
            } else {
                backdrop.classList.remove('show');
                setTimeout(() => backdrop.style.display = 'none', 300);
            }
        }
    </script>
</div>

@else
{{-- ── Guest layout ─────────────────────────────── --}}
<main style="min-height:100vh;display:grid;place-items:center;padding:2rem 1rem;background:linear-gradient(150deg,#f0f4ff 0%,#f8fafc 40%,#eef2fb 100%)">
    @if(session('status'))
        <div class="alert alert-success" style="position:fixed;top:1.25rem;left:50%;transform:translateX(-50%);z-index:99;white-space:nowrap">
            {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="position:fixed;top:1.25rem;left:50%;transform:translateX(-50%);z-index:99;white-space:nowrap">
            {{ $errors->first() }}
        </div>
    @endif
    @yield('content')
</main>
@endauth

@stack('scripts')
<script>
    window.confirmAction = function(message, onConfirm) {
        const dialog = document.createElement('div');
        dialog.innerHTML = `
            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity duration-200" id="confirm-overlay">
                <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full transform transition-all duration-200 scale-95 opacity-0" id="confirm-modal-box">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">Konfirmasi</h3>
                            <p class="text-sm text-slate-500 mt-1">${message}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button id="confirm-cancel" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                        <button id="confirm-ok" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-lg shadow-red-500/30 transition-all">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(dialog);
        
        requestAnimationFrame(() => {
            const box = document.getElementById('confirm-modal-box');
            if (box) {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }
        });

        const cleanup = () => {
            const box = document.getElementById('confirm-modal-box');
            const overlay = document.getElementById('confirm-overlay');
            if (box && overlay) {
                box.classList.remove('scale-100', 'opacity-100');
                box.classList.add('scale-95', 'opacity-0');
                overlay.style.opacity = '0';
                setTimeout(() => dialog.remove(), 200);
            } else {
                dialog.remove();
            }
        };

        document.getElementById('confirm-cancel').onclick = cleanup;
        document.getElementById('confirm-ok').onclick = () => {
            cleanup();
            onConfirm();
        };
    };
</script>
</body>
</html>
