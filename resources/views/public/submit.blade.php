<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Ticket — Heaven Scent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6366f1;
            --primary-d: #4f46e5;
            --secondary: #8b5cf6;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(150deg, #f0f4ff 0%, #f8fafc 50%, #eef2fb 100%);
            color: var(--ink);
            line-height: 1.6;
        }

        /* ── Layout ─────────────────────────────────── */
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(226,232,240,.7);
            padding: .875rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .logo-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: grid;
            place-items: center;
            font-weight: 900;
            font-size: .9375rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(99,102,241,.35);
            flex-shrink: 0;
        }

        .logo-text { font-size: .9375rem; font-weight: 800; color: var(--ink); }
        .logo-sub  { font-size: .6875rem; color: var(--muted); margin-top: 1px; }

        .hero {
            text-align: center;
            padding: 3rem 1.5rem 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            background: linear-gradient(135deg, rgba(99,102,241,.12), rgba(139,92,246,.08));
            border: 1px solid rgba(99,102,241,.2);
            border-radius: 999px;
            padding: .3125rem .875rem;
            font-size: .75rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: .02em;
            margin-bottom: 1.125rem;
        }

        .hero h1 {
            font-size: clamp(1.5rem, 4vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--ink);
            line-height: 1.15;
        }

        .hero p {
            margin-top: .625rem;
            font-size: .9375rem;
            color: var(--muted);
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Form card ───────────────────────────────── */
        .form-wrap {
            flex: 1;
            padding: 0 1rem 3rem;
            display: flex;
            justify-content: center;
        }

        .form-card {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,.7);
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px rgba(15,23,42,.04), 0 24px 48px rgba(15,23,42,.08);
            padding: 2rem;
            width: 100%;
            max-width: 640px;
        }

        /* ── Steps indicator ─────────────────────────── */
        .steps {
            display: flex;
            gap: 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--line);
            padding-bottom: 1.25rem;
        }

        .step {
            flex: 1;
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e2e8f0;
            display: grid;
            place-items: center;
            font-size: .6875rem;
            font-weight: 700;
            flex-shrink: 0;
            transition: background .2s, color .2s;
        }

        .step.active .step-num { background: var(--primary); color: #fff; }
        .step.active { color: var(--primary); }
        .step.done .step-num { background: #10b981; color: #fff; }
        .step.done { color: #10b981; }

        .step-divider { flex: 1; height: 1px; background: var(--line); margin: 0 .5rem; align-self: center; }

        /* ── Form primitives ─────────────────────────── */
        .field { display: flex; flex-direction: column; gap: .375rem; }

        .field label, .field-label {
            font-size: .8125rem;
            font-weight: 600;
            color: #374151;
        }

        .field label .hint { font-weight: 400; color: #94a3b8; margin-left: .25rem; }

        .inp {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: .625rem;
            padding: .625rem .875rem;
            font-size: .9rem;
            color: var(--ink);
            font-family: inherit;
            outline: none;
            width: 100%;
            transition: border-color .15s, box-shadow .15s;
        }

        .inp:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        textarea.inp { resize: vertical; }
        select.inp { cursor: pointer; }

        /* ── Store combobox ──────────────────────────── */
        .combobox { position: relative; }

        .combobox-input-wrap { position: relative; }

        .combobox-icon {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #94a3b8;
            transition: transform .2s;
        }
        .combobox.open .combobox-icon { transform: translateY(-50%) rotate(180deg); }

        .combobox-menu {
            position: absolute;
            z-index: 30;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: .875rem;
            box-shadow: 0 8px 32px rgba(15,23,42,.12), 0 2px 8px rgba(15,23,42,.06);
            max-height: 280px;
            overflow-y: auto;
            padding: .375rem;
            display: none;
            animation: menuIn .15s ease;
        }

        @keyframes menuIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .combobox.open .combobox-menu { display: block; }

        .combobox-menu::-webkit-scrollbar { width: 4px; }
        .combobox-menu::-webkit-scrollbar-track { background: transparent; }
        .combobox-menu::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

        .store-opt {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .625rem .75rem;
            border-radius: .5rem;
            cursor: pointer;
            transition: background .12s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .store-opt:hover, .store-opt:focus { background: #f5f3ff; outline: none; }
        .store-opt.selected { background: linear-gradient(135deg, rgba(99,102,241,.1), rgba(139,92,246,.06)); }

        .store-opt-name { font-size: .875rem; font-weight: 600; color: var(--ink); }
        .store-opt-prov { font-size: .75rem; color: var(--muted); margin-top: 1px; }
        .store-opt-code {
            flex-shrink: 0;
            font-size: .6875rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #64748b;
            border-radius: .375rem;
            padding: .2rem .5rem;
            letter-spacing: .03em;
        }

        .store-opt mark {
            background: rgba(99,102,241,.15);
            color: var(--primary);
            border-radius: 2px;
        }

        .store-empty {
            padding: 1.5rem;
            text-align: center;
            font-size: .875rem;
            color: #94a3b8;
        }

        /* ── Selected store chip ─────────────────────── */
        .store-chip {
            display: flex;
            align-items: center;
            gap: .625rem;
            background: linear-gradient(135deg, rgba(99,102,241,.1), rgba(139,92,246,.06));
            border: 1.5px solid rgba(99,102,241,.2);
            border-radius: .625rem;
            padding: .5625rem .875rem;
            margin-top: .375rem;
        }

        .store-chip-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .store-chip-name { font-size: .875rem; font-weight: 700; color: var(--ink); }
        .store-chip-prov { font-size: .75rem; color: var(--muted); }
        .store-chip-clear {
            margin-left: auto;
            color: #94a3b8;
            cursor: pointer;
            background: none;
            border: none;
            padding: .25rem;
            border-radius: .375rem;
            transition: background .15s;
        }
        .store-chip-clear:hover { background: rgba(239,68,68,.08); color: #ef4444; }

        /* ── File upload ─────────────────────────────── */
        .file-zone {
            position: relative;
            border: 2px dashed #e2e8f0;
            border-radius: .75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }
        .file-zone:hover { border-color: var(--primary); background: rgba(99,102,241,.02); }
        .file-zone.has-file { border-color: #10b981; background: rgba(16,185,129,.03); }
        .file-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .file-zone-icon { width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto .625rem; }
        .file-zone-label { font-size: .875rem; font-weight: 600; color: var(--muted); }
        .file-zone-sub { font-size: .75rem; color: #94a3b8; margin-top: .25rem; }
        .file-zone-name { font-size: .875rem; font-weight: 600; color: #10b981; margin-top: .25rem; }

        /* ── Submit button ───────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: .875rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            border: none;
            border-radius: .75rem;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(99,102,241,.35);
            transition: transform .18s, box-shadow .18s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            letter-spacing: -.01em;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99,102,241,.45); }
        .submit-btn:active { transform: translateY(0); }

        /* ── Alert ───────────────────────────────────── */
        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: .75rem;
            padding: .875rem 1rem;
            color: #b91c1c;
            font-size: .875rem;
            display: flex;
            gap: .625rem;
            align-items: flex-start;
        }

        /* ── Grid ────────────────────────────────────── */
        .grid-2 { display: grid; grid-template-columns: 1fr; gap: 1.125rem; }
        @media (min-width: 480px) { .grid-2 { grid-template-columns: 1fr 1fr; } }

        /* ── Footer ──────────────────────────────────── */
        footer {
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: .75rem;
            color: #94a3b8;
            border-top: 1px solid rgba(226,232,240,.5);
        }

        footer a { color: #6366f1; text-decoration: none; font-weight: 600; }

        /* ── Char counter ────────────────────────────── */
        .char-count { font-size: .75rem; color: #94a3b8; text-align: right; }
        .char-count.warn { color: #f59e0b; }
        .char-count.err  { color: #ef4444; }
    </style>
</head>
<body>
<div class="page">

    {{-- Topbar --}}
    <div class="topbar" style="justify-content: space-between;">
        <div style="display:flex; align-items:center; gap:.75rem;">
            <div class="logo-mark">A</div>
            <div>
                <div class="logo-text">Ajuin</div>
                <div class="logo-sub">Heaven Scent Operations</div>
            </div>
        </div>
        <a href="{{ route('public.track.search') }}" style="background:#f1f5f9; color:#475569; font-weight:600; padding:.4rem .8rem; border-radius:.5rem; font-size:.75rem; text-decoration:none; display:flex; align-items:center; gap:.3rem; transition: background .2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
            <svg style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            Lacak
        </a>
    </div>

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-badge">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/></svg>
            Form Pengajuan Publik
        </div>
        <h1>Ajukan Kebutuhan Toko</h1>
        <p>Sampaikan kebutuhan atau kendala toko Anda — tim kami akan segera menangani.</p>
    </div>

    {{-- Form --}}
    <div class="form-wrap">
        <div class="form-card">

            {{-- Steps --}}
            <div class="steps">
                <div class="step active" id="step1-indicator">
                    <span class="step-num">1</span>
                    <span>Identitas</span>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="step2-indicator">
                    <span class="step-num">2</span>
                    <span>Detail</span>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="step3-indicator">
                    <span class="step-num">3</span>
                    <span>Kirim</span>
                </div>
            </div>

            @if($errors->any())
            <div class="alert-err" style="margin-bottom:1.25rem">
                <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <div>
                    <strong>Terdapat kesalahan:</strong>
                    <ul style="margin-top:.25rem;padding-left:1rem">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form method="post" action="{{ route('public.submit.store') }}"
                  enctype="multipart/form-data" id="ticket-form" novalidate>
                @csrf

                {{-- Step 1: Identitas --}}
                <div id="step-1" style="display:flex;flex-direction:column;gap:1.125rem">

                    {{-- Store combobox --}}
                    <div class="field">
                        <label for="store-search">
                            Lokasi <span class="hint">(toko / gudang / mess)</span>
                            <span style="color:#ef4444">*</span>
                        </label>
                        <input type="hidden" name="store_id" id="store_id" value="{{ old('store_id') }}">

                        <div class="combobox {{ old('store_id') ? 'has-selected' : '' }}" id="combobox-root">
                            <div class="combobox-input-wrap">
                                <input
                                    id="store-search"
                                    type="text"
                                    class="inp"
                                    style="padding-right:2.5rem"
                                    placeholder="Ketik nama atau kode toko..."
                                    autocomplete="off"
                                    spellcheck="false"
                                >
                                <svg class="combobox-icon" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>

                            <div class="combobox-menu" id="store-menu">
                                @forelse($stores as $store)
                                <button
                                    type="button"
                                    class="store-opt {{ old('store_id') == $store->id ? 'selected' : '' }}"
                                    data-id="{{ $store->id }}"
                                    data-name="{{ $store->name }}"
                                    data-code="{{ $store->code }}"
                                    data-search="{{ strtolower($store->name.' '.$store->code) }}"
                                >
                                    <span>
                                        <span class="store-opt-name">{{ $store->name }}</span>
                                    </span>
                                    <span class="store-opt-code">{{ $store->code }}</span>
                                </button>
                                @empty
                                <div class="store-empty">Belum ada toko terdaftar.</div>
                                @endforelse
                                <div class="store-empty" id="no-results" style="display:none">Toko tidak ditemukan.</div>
                            </div>
                        </div>

                        {{-- Selected chip --}}
                        <div id="store-chip" style="{{ old('store_id') ? '' : 'display:none' }}">
                            @php $sel = $stores->firstWhere('id', (int) old('store_id')); @endphp
                            <div class="store-chip">
                                <div class="store-chip-icon">
                                    <svg style="width:15px;height:15px;color:#fff" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                                </div>
                                <div>
                                    <div class="store-chip-name" id="chip-name">{{ $sel?->name ?? '' }}</div>
                                </div>
                                <button type="button" class="store-chip-clear" id="chip-clear" title="Ganti toko">
                                    <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Nama pengaju --}}
                    <div class="field">
                        <label for="submitted_by">Nama Kamu <span style="color:#ef4444">*</span></label>
                        <input id="submitted_by" name="submitted_by" type="text"
                            value="{{ old('submitted_by') }}"
                            class="inp" placeholder="Nama lengkap / panggilan" required>
                    </div>

                    {{-- Jabatan --}}
                    <div class="field">
                        <label for="jabatan">Jabatan <span style="color:#ef4444">*</span></label>
                        <input id="jabatan" name="jabatan" type="text"
                            value="{{ old('jabatan') }}"
                            class="inp" placeholder="cth. Kepala Toko, SPV, Staff" required>
                    </div>

                    <div style="display:flex;justify-content:flex-end;padding-top:.25rem">
                        <button type="button" id="next-btn"
                            style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:.625rem;padding:.625rem 1.5rem;font-size:.9rem;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:.4rem;transition:all .18s">
                            Lanjutkan
                            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Step 2: Detail --}}
                <div id="step-2" style="display:none;flex-direction:column;gap:1.125rem">

                    <div class="grid-2">
                        {{-- Jenis --}}
                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="type">Jenis Pengajuan <span style="color:#ef4444">*</span></label>
                            <select id="type" name="type" class="inp" required>
                                @foreach(config('ajuin.ticket_types') as $key => $label)
                                <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="field">
                        <label for="description">Deskripsi <span style="color:#ef4444">*</span></label>
                        <textarea id="description" name="description" rows="5" class="inp"
                            placeholder="Ceritakan kebutuhan atau kendala toko dengan jelas — semakin detail semakin cepat ditangani."
                            required minlength="10" maxlength="2000">{{ old('description') }}</textarea>
                        <div class="char-count" id="char-count">0 / 2000 karakter</div>
                    </div>

                    {{-- Attachment --}}
                    <div class="field">
                        <label>Lampiran <span style="color:#ef4444">*</span> <span class="hint">(wajib, jpg/png/pdf, maks. 2MB per file, maks. 5 file)</span></label>
                        <div class="file-zone" id="file-zone">
                            <input type="file" name="attachments[]" id="attachment-input"
                                accept=".jpg,.jpeg,.png,.pdf" multiple>
                            <div class="file-zone-icon" id="file-zone-icon">
                                <svg style="width:18px;height:18px;color:#94a3b8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                            </div>
                            <div class="file-zone-label" id="file-label">Klik atau seret file ke sini</div>
                            <div class="file-zone-sub" id="file-zone-sub">JPG, PNG, atau PDF (maks. 2 MB)</div>
                        </div>
                        <div id="file-list" style="margin-top: 0.5rem; display: none; flex-direction: column; gap: 0.5rem;"></div>
                        <button type="button" id="add-more-files-btn" style="display: none; margin-top: 0.25rem; background: #fff; color: #6366f1; border: 1.5px dashed #6366f1; border-radius: 0.625rem; padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; width: 100%; text-align: center; transition: background .15s;" onmouseover="this.style.background='#f5f3ff'" onmouseout="this.style.background='#fff'">
                            + Tambah File Lainnya
                        </button>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:.25rem">
                        <button type="button" id="back-btn"
                            style="background:#fff;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;padding:.5625rem 1.125rem;font-size:.875rem;font-weight:600;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:.4rem;transition:background .15s">
                            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                            Kembali
                        </button>
                        <button type="submit" class="submit-btn" style="width:auto;padding:.625rem 1.75rem;font-size:.9375rem">
                            <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                            Submit Ticket
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <footer>
        © {{ date('Y') }} Heaven Scent Indonesia &mdash;
        <a href="{{ url('/') }}">Kembali ke beranda</a>
    </footer>
</div>

<script>
(function () {
    /* ── Store combobox ──────────────────────────────────── */
    const root      = document.getElementById('combobox-root');
    const searchInp = document.getElementById('store-search');
    const hiddenInp = document.getElementById('store_id');
    const menu      = document.getElementById('store-menu');
    const noResults = document.getElementById('no-results');
    const chip      = document.getElementById('store-chip');
    const chipName  = document.getElementById('chip-name');
    const chipClear = document.getElementById('chip-clear');
    const opts      = Array.from(menu.querySelectorAll('.store-opt'));

    let selectedId = hiddenInp.value || '';

    function openMenu()  { root.classList.add('open'); }
    function closeMenu() { root.classList.remove('open'); }

    function highlightText(text, query) {
        if (!query) return text;
        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp(`(${escaped})`, 'gi'), '<mark>$1</mark>');
    }

    function filterOpts(query) {
        const q = query.trim().toLowerCase();
        let visible = 0;
        opts.forEach(opt => {
            const match = !q || opt.dataset.search.includes(q);
            opt.style.display = match ? '' : 'none';
            if (match) {
                visible++;
                // Update highlight
                opt.querySelector('.store-opt-name').innerHTML = highlightText(opt.dataset.name, query);
            } else {
                opt.querySelector('.store-opt-name').innerHTML = opt.dataset.name;
            }
        });
        noResults.style.display = visible === 0 ? '' : 'none';
    }

    function selectStore(id, name) {
        selectedId = id;
        hiddenInp.value = id;
        chipName.textContent = name;
        chip.style.display = '';
        searchInp.closest('.combobox-input-wrap').style.display = 'none';
        opts.forEach(o => o.classList.toggle('selected', o.dataset.id === id));
        closeMenu();
    }

    function clearStore() {
        selectedId = '';
        hiddenInp.value = '';
        chip.style.display = 'none';
        searchInp.closest('.combobox-input-wrap').style.display = '';
        searchInp.value = '';
        opts.forEach(o => o.classList.remove('selected'));
        filterOpts('');
        searchInp.focus();
    }

    // Init if pre-selected (from old())
    if (selectedId) {
        const pre = opts.find(o => o.dataset.id === selectedId);
        if (pre) {
            chipName.textContent = pre.dataset.name;
            searchInp.closest('.combobox-input-wrap').style.display = 'none';
        }
    }

    searchInp.addEventListener('focus', () => { filterOpts(searchInp.value); openMenu(); });
    searchInp.addEventListener('input', () => { filterOpts(searchInp.value); openMenu(); });

    opts.forEach(opt => {
        opt.addEventListener('mousedown', e => {
            e.preventDefault(); // prevent blur before click registers
            selectStore(opt.dataset.id, opt.dataset.name, opt.dataset.prov);
        });
    });

    chipClear.addEventListener('click', clearStore);

    document.addEventListener('click', e => {
        if (!root.contains(e.target)) closeMenu();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeMenu();
    });

    /* ── Multi-step ─────────────────────────────────────── */
    const step1El = document.getElementById('step-1');
    const step2El = document.getElementById('step-2');
    const nextBtn = document.getElementById('next-btn');
    const backBtn = document.getElementById('back-btn');
    const s1ind   = document.getElementById('step1-indicator');
    const s2ind   = document.getElementById('step2-indicator');
    const s3ind   = document.getElementById('step3-indicator');

    function goStep2() {
        // Validate step 1
        if (!hiddenInp.value) {
            searchInp.focus();
            searchInp.closest('.combobox-input-wrap').style.display = '';
            openMenu();
            filterOpts('');
            searchInp.reportValidity && searchInp.setCustomValidity('Pilih toko terlebih dahulu.');
            return;
        }
        searchInp.setCustomValidity && searchInp.setCustomValidity('');
        const subBy = document.getElementById('submitted_by');
        if (!subBy.value.trim()) { subBy.focus(); return; }
        const jabatan = document.getElementById('jabatan');
        if (!jabatan.value.trim()) { jabatan.focus(); return; }

        step1El.style.display = 'none';
        step2El.style.display = 'flex';
        s1ind.classList.remove('active');
        s1ind.classList.add('done');
        s2ind.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goStep1() {
        step2El.style.display = 'none';
        step1El.style.display = 'flex';
        s2ind.classList.remove('active');
        s1ind.classList.remove('done');
        s1ind.classList.add('active');
    }

    nextBtn.addEventListener('click', goStep2);
    backBtn.addEventListener('click', goStep1);

    // If there were server-side errors, go straight to step 2
    @if($errors->has('type') || $errors->has('description') || $errors->has('attachment'))
    goStep2();
    @endif

    /* ── Form submit → step 3 indicator ─────────────────── */
    document.getElementById('ticket-form').addEventListener('submit', function(e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            fileZone.style.borderColor = '#ef4444';
            fileZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Lampiran wajib diisi minimal 1 file.');
            return;
        }
        s2ind.classList.remove('active');
        s2ind.classList.add('done');
        s3ind.classList.add('active');
    });

    /* ── Char counter ────────────────────────────────────── */
    const descTA = document.getElementById('description');
    const counter = document.getElementById('char-count');
    function updateCount() {
        const n = descTA.value.length;
        counter.textContent = `${n} / 2000 karakter`;
        counter.className = 'char-count' + (n > 1900 ? ' err' : n > 1600 ? ' warn' : '');
    }
    descTA.addEventListener('input', updateCount);
    updateCount();

    /* ── File upload zone ────────────────────────────────── */
    const fileZone  = document.getElementById('file-zone');
    const fileInput = document.getElementById('attachment-input');
    const fileLabel = document.getElementById('file-label');
    const fileList = document.getElementById('file-list');
    const addMoreBtn = document.getElementById('add-more-files-btn');
    
    let selectedFiles = [];

    function renderFileList() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;

        fileList.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            fileZone.style.display = 'none';
            fileList.style.display = 'flex';
            
            selectedFiles.forEach((f, index) => {
                const item = document.createElement('div');
                item.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.875rem; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 0.625rem; box-shadow: 0 1px 2px rgba(0,0,0,.02);';
                item.innerHTML = `
                    <div style="font-size: 0.875rem; color: #334155; display: flex; align-items: center; gap: 0.625rem; overflow: hidden; font-weight: 500;">
                        <svg style="width:18px;height:18px;color:#94a3b8;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">${f.name}</span>
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 400;">(${(f.size / 1024 / 1024).toFixed(2)} MB)</span>
                    </div>
                    <button type="button" class="remove-file-btn" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; transition: background .15s;" title="Hapus file">
                        <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                
                const removeBtn = item.querySelector('.remove-file-btn');
                removeBtn.addEventListener('mouseover', () => removeBtn.style.background = 'rgba(239,68,68,.08)');
                removeBtn.addEventListener('mouseout', () => removeBtn.style.background = 'none');
                removeBtn.addEventListener('click', () => {
                    selectedFiles.splice(index, 1);
                    renderFileList();
                });
                
                fileList.appendChild(item);
            });
            
            if (selectedFiles.length < 5) {
                addMoreBtn.style.display = 'block';
            } else {
                addMoreBtn.style.display = 'none';
            }
        } else {
            fileZone.style.display = 'block';
            fileList.style.display = 'none';
            addMoreBtn.style.display = 'none';
            fileZone.classList.remove('has-file');
            fileLabel.textContent = 'Klik atau seret file ke sini';
            fileLabel.style.color = '';
        }
    }

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(newFile => {
                const isDuplicate = selectedFiles.some(f => f.name === newFile.name && f.size === newFile.size);
                if (!isDuplicate && selectedFiles.length < 5) {
                    selectedFiles.push(newFile);
                }
            });
        }
        renderFileList();
    });

    addMoreBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag-and-drop
    fileZone.addEventListener('dragover', e => { e.preventDefault(); fileZone.style.borderColor = '#6366f1'; });
    fileZone.addEventListener('dragleave', ()  => { fileZone.style.borderColor = ''; });
    fileZone.addEventListener('drop', e => {
        e.preventDefault();
        fileZone.style.borderColor = '';
        if (e.dataTransfer.files.length > 0) {
            Array.from(e.dataTransfer.files).forEach(newFile => {
                const isDuplicate = selectedFiles.some(f => f.name === newFile.name && f.size === newFile.size);
                if (!isDuplicate && selectedFiles.length < 5) {
                    selectedFiles.push(newFile);
                }
            });
            renderFileList();
        }
    });
})();
</script>
</body>
</html>
