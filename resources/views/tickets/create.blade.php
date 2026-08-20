@extends('layouts.app', ['title' => 'Buat Ticket Internal'])

@section('content')
<style>
    .file-zone {
        position: relative;
        border: 2px dashed #e2e8f0;
        border-radius: .75rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .file-zone:hover { border-color: #111827; background: rgba(15,23,42,.02); }
    .file-zone.has-file { border-color: #10b981; background: rgba(16,185,129,.03); }
    .file-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .file-zone-icon { width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto .625rem; }
    .file-zone-label { font-size: .875rem; font-weight: 600; color: #64748b; }
    .file-zone-sub { font-size: .75rem; color: #94a3b8; margin-top: .25rem; }
</style>
<div style="margin-bottom:1.5rem">
    <p class="eyebrow">Internal Initiative</p>
    <h1 class="page-title" style="margin-top:.25rem">Buat Ticket Internal</h1>
    <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Ticket dibuat atas nama toko yang berada dalam scope data Anda.</p>
</div>

<div class="card" style="max-width:700px;padding:2rem">
    <form method="post" action="{{ route('tickets.store') }}" id="create-ticket-form" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;gap:1.25rem">

            <div>
                <label class="form-label" for="store_id">Lokasi <span style="font-weight:400;color:#94a3b8">(toko / gudang / mess)</span></label>
                <select id="store_id" name="store_id" class="form-input" required>
                    <option value="">— Pilih lokasi —</option>
                    @foreach($stores as $store)
                    <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>{{ $store->name }} ({{ $store->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" for="type">Jenis Pengajuan</label>
                <select id="type" name="type" class="form-input" required>
                    @foreach(config('ajuin.ticket_types') as $key => $label)
                    <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div id="maintenance-type-field" style="display:none">
                <label class="form-label" for="maintenance_type_id">Jenis Maintenance</label>
                <select id="maintenance_type_id" name="maintenance_type_id" class="form-input">
                    <option value="">— Pilih jenis maintenance —</option>
                    @foreach($maintenanceTypes as $mt)
                    <option value="{{ $mt->id }}" data-tier="{{ $mt->tier->name }}" data-deadline-days="{{ $mt->tier->deadline_days }}"
                        @selected(old('maintenance_type_id') == $mt->id)>{{ $mt->name }} (Tier {{ $mt->tier->name }})</option>
                    @endforeach
                </select>
                <div id="maintenance-tier-badge" style="display:none;margin-top:.625rem;padding:.625rem .875rem;border-radius:.625rem;background:#fef3c7;border:1px solid #fde68a;font-size:.8125rem;color:#92400e;font-weight:600"></div>
            </div>

            <div id="recommendation-links-field" style="display:none">
                <label class="form-label">Link Rekomendasi <span style="font-weight:400;color:#94a3b8">(opsional, bisa lebih dari satu)</span></label>
                <div id="recommendation-links-list" style="display:flex;flex-direction:column;gap:.625rem"></div>
                <button type="button" id="add-link-btn" style="margin-top:.5rem;background:#fff;color:#111827;border:1.5px dashed #111827;border-radius:.625rem;padding:.625rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;width:100%;text-align:center;transition:background .15s" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    + Tambah Link
                </button>
            </div>

            <div>
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="5" class="form-input"
                    placeholder="Jelaskan detail pengajuan (min. 10 karakter)…" required>{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="form-label">Lampiran <span style="color:#ef4444">*</span> <span style="font-weight:400;color:#94a3b8;margin-left:.25rem">(Wajib, min. 1 file)</span></label>
                <div class="file-zone" id="file-zone">
                    <input type="file" name="attachments[]" id="attachment-input"
                        accept=".jpg,.jpeg,.png,.pdf" multiple>
                    <div class="file-zone-icon" id="file-zone-icon">
                        <svg style="width:18px;height:18px;color:#94a3b8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                    </div>
                    <div class="file-zone-label" id="file-label">Klik atau seret file ke sini</div>
                    <div class="file-zone-sub" id="file-zone-sub">JPG, PNG, atau PDF (maks. 2 MB per file, maks. 5 file)</div>
                </div>
                <div id="file-list" style="margin-top: 0.5rem; display: none; flex-direction: column; gap: 0.5rem;"></div>
                <button type="button" id="add-more-files-btn" style="display: none; margin-top: 0.25rem; background: #fff; color: #111827; border: 1.5px dashed #111827; border-radius: 0.625rem; padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; width: 100%; text-align: center; transition: background .15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    + Tambah File Lainnya
                </button>
            </div>

            <div style="display:flex;align-items:center;gap:.75rem;padding-top:.25rem">
                <button type="submit" class="btn btn-primary">
                    <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Simpan Ticket
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Batal</a>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#store_id').select2({
            placeholder: "— Pilih toko —",
            allowClear: true
        });

        const typeSelect = document.getElementById('type');

        /* ── Jenis Maintenance (hanya untuk tipe Maintenance) ── */
        const maintenanceField     = document.getElementById('maintenance-type-field');
        const maintenanceSelect    = document.getElementById('maintenance_type_id');
        const maintenanceTierBadge = document.getElementById('maintenance-tier-badge');
        const attachmentHint       = document.getElementById('file-zone-sub');

        function updateMaintenanceBadge() {
            const opt = maintenanceSelect.options[maintenanceSelect.selectedIndex];
            if (opt && opt.value) {
                maintenanceTierBadge.textContent = `Tier ${opt.dataset.tier} — Deadline ${opt.dataset.deadlineDays} hari sejak ticket dibuat`;
                maintenanceTierBadge.style.display = 'block';
            } else {
                maintenanceTierBadge.style.display = 'none';
            }
        }

        function toggleMaintenanceField() {
            const isMaintenance = typeSelect.value === 'MAINTENANCE';
            maintenanceField.style.display = isMaintenance ? 'block' : 'none';
            maintenanceSelect.required = isMaintenance;
            if (!isMaintenance) {
                maintenanceSelect.value = '';
                maintenanceTierBadge.style.display = 'none';
            }

            // Lampiran maintenance hanya foto — tidak menerima PDF
            if (attachmentHint) {
                attachmentHint.textContent = isMaintenance
                    ? 'JPG atau PNG saja (maks. 2 MB per file, maks. 5 file)'
                    : 'JPG, PNG, atau PDF (maks. 2 MB per file, maks. 5 file)';
            }
            if (fileInput) {
                fileInput.setAttribute('accept', isMaintenance ? '.jpg,.jpeg,.png' : '.jpg,.jpeg,.png,.pdf');
            }
        }

        maintenanceSelect.addEventListener('change', updateMaintenanceBadge);
        typeSelect.addEventListener('change', toggleMaintenanceField);

        /* ── Link rekomendasi (hanya untuk Pembelian Peralatan) ── */
        const linksField     = document.getElementById('recommendation-links-field');
        const linksList      = document.getElementById('recommendation-links-list');
        const addLinkBtn     = document.getElementById('add-link-btn');
        const MAX_LINKS      = 10;

        function addLinkRow(value = '') {
            if (linksList.children.length >= MAX_LINKS) return;

            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:.5rem;align-items:center';
            row.innerHTML = `
                <input type="url" name="recommendation_links[]" value="${value.replace(/"/g, '&quot;')}"
                    class="form-input" placeholder="https://…">
                <button type="button" class="remove-link-btn" title="Hapus link"
                    style="color:#ef4444;background:none;border:none;cursor:pointer;padding:.35rem;border-radius:.375rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;
            row.querySelector('.remove-link-btn').addEventListener('click', () => row.remove());
            linksList.appendChild(row);
        }

        function toggleLinksField() {
            if (typeSelect.value === 'PEMBELIAN_PERALATAN') {
                linksField.style.display = 'block';
                if (linksList.children.length === 0) addLinkRow();
            } else {
                linksField.style.display = 'none';
                linksList.innerHTML = '';
            }
        }

        typeSelect.addEventListener('change', toggleLinksField);
        addLinkBtn.addEventListener('click', () => addLinkRow());
        toggleLinksField();

        /* ── File upload zone ────────────────────────────────── */
        const fileZone  = document.getElementById('file-zone');
        const fileInput = document.getElementById('attachment-input');
        const fileLabel = document.getElementById('file-label');
        const fileList = document.getElementById('file-list');
        const addMoreBtn = document.getElementById('add-more-files-btn');

        toggleMaintenanceField();
        updateMaintenanceBadge();

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

        if (fileInput) {
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
            fileZone.addEventListener('dragover', e => { e.preventDefault(); fileZone.style.borderColor = '#111827'; });
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
        }

        // Lampiran wajib — blok submit jika kosong
        document.getElementById('create-ticket-form').addEventListener('submit', function(e) {
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                fileZone.style.borderColor = '#ef4444';
                fileZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert('Lampiran wajib diisi minimal 1 file.');
            }
        });
    });
</script>
@endpush
