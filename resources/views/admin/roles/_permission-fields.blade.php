{{-- $permissions: grouped Collection<string, Collection<Permission>>, $role: Role|null, $restricted: string[] --}}
@php $restricted ??= []; @endphp
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem">
    @foreach($permissions as $group => $items)
    <div class="perm-group" style="border:1px solid #f1f5f9;border-radius:.625rem;padding:.875rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem;padding-bottom:.5rem;border-bottom:1px solid #f1f5f9">
            <span style="font-size:.75rem;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:.03em">{{ config('ajuin.permission_group_labels')[$group] ?? ucfirst($group) }}</span>
            <label style="display:flex;align-items:center;gap:.3rem;font-size:.6875rem;font-weight:600;color:#94a3b8;cursor:pointer">
                <input type="checkbox" class="perm-toggle-all" style="width:13px;height:13px;accent-color:#111827" onclick="toggleGroup(this)">
                Semua
            </label>
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem">
            @foreach($items as $permission)
            @php $isRestricted = in_array($permission->name, $restricted, true); @endphp
            <label style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;color:{{ $isRestricted ? '#cbd5e1' : '#374151' }};cursor:{{ $isRestricted ? 'not-allowed' : 'pointer' }}"
                   @if($isRestricted) title="Permission ini dikunci untuk role {{ $role->name }}" @endif>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                       @checked($role?->hasPermissionTo($permission->name))
                       @disabled($isRestricted)
                       style="width:14px;height:14px;accent-color:#111827;flex-shrink:0">
                <span>{{ config('ajuin.permission_labels')[$permission->name] ?? $permission->name }}</span>
                @if($isRestricted)
                <svg style="width:12px;height:12px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                @endif
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
