@extends('layouts.app', ['title' => 'Role Management'])

@section('content')
<h1 class="mb-5 text-2xl font-semibold">Role Management</h1>
<form method="post" action="{{ route('admin.roles.store') }}" class="mb-6 rounded border bg-white p-4">
    @csrf
    <div class="grid gap-3 md:grid-cols-3"><input name="name" placeholder="Nama role baru" class="rounded border px-3 py-2" required><button class="rounded bg-blue-700 px-4 py-2 text-white">Tambah Role</button></div>
    <div class="mt-4 grid gap-3 md:grid-cols-4">@foreach($permissions as $group => $items)<div><h3 class="font-semibold">{{ $group }}</h3>@foreach($items as $permission)<label class="block text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}"> {{ $permission->name }}</label>@endforeach</div>@endforeach</div>
</form>
<div class="space-y-4">
@foreach($roles as $role)
    <form method="post" action="{{ route('admin.roles.update', $role) }}" class="rounded border bg-white p-4">
        @csrf @method('PUT')
        <div class="mb-3 flex items-center gap-3"><input name="name" value="{{ $role->name }}" @disabled($role->name === 'Super Admin') class="rounded border px-3 py-2 font-semibold"><span class="text-sm text-slate-500">{{ $role->users_count }} user</span><button class="rounded bg-slate-900 px-3 py-2 text-white" @disabled($role->name === 'Super Admin')>Simpan</button></div>
        <div class="grid gap-3 md:grid-cols-4">@foreach($permissions as $group => $items)<div><h3 class="font-semibold">{{ $group }}</h3>@foreach($items as $permission)<label class="block text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($role->hasPermissionTo($permission->name)) @disabled($role->name === 'Super Admin')> {{ $permission->name }}</label>@endforeach</div>@endforeach</div>
    </form>
@endforeach
</div>
@endsection
