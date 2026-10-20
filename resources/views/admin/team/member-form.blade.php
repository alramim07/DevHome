@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <h2 class="mb-4">{{ $mode === 'create' ? 'Create User' : 'Edit User: ' . $member->name }}</h2>

        <form action="{{ $mode === 'create' ? route('team.members.store') : route('team.members.update', $member) }}"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control bg-dark @error('name') is-invalid @enderror"
                            value="{{ old('name', $member?->name) }}" required>
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                            class="form-control bg-dark @error('email') is-invalid @enderror"
                            value="{{ old('email', $member?->email) }}" required>
                        @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password {{ $mode === 'edit' ? '(leave empty to keep current)' : '' }}</label>
                        <input type="password" name="password"
                            class="form-control bg-dark @error('password') is-invalid @enderror"
                            {{ $mode === 'create' ? 'required' : '' }}>
                        @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select bg-dark @error('role') is-invalid @enderror" required>
                            @foreach ($rolesLabels as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('role', $member?->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position <span class="text-muted small">(preset pre-fills permissions
                                below)</span></label>
                        <select name="position_id" id="positionSelect" class="form-select bg-dark">
                            <option value="">— None —</option>
                            @foreach ($positions as $position)
                            <option value="{{ $position->id }}"
                                data-permissions="{{ json_encode($position->permissions ?? []) }}"
                                {{ (int) old('position_id', $selectedPositionId) === $position->id ? 'selected' : '' }}>
                                {{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="image" class="form-control bg-dark @error('image') is-invalid @enderror"
                            accept="image/*">
                        @if ($member?->image)
                        <img src="{{ media_url($member->image) }}" alt="" class="mt-2 rounded-circle" width="48"
                            height="48" style="object-fit: cover;">
                        @endif
                        @error('image')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label d-block">Permissions</label>
                        <div class="d-flex flex-wrap gap-3 border rounded p-3">
                            @foreach ($sectionsLabels as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]"
                                    value="{{ $key }}" id="perm-{{ $key }}"
                                    {{ in_array($key, old('permissions', $presetPermissions)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $key }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <p class="text-muted small mt-1">Per-user permissions are final — they don't change when the
                            position preset changes later.</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Teams</label>
                        <div class="d-flex flex-wrap gap-3 border rounded p-3">
                            @forelse ($teams as $team)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="team_ids[]"
                                    value="{{ $team->id }}" id="team-{{ $team->id }}"
                                    {{ in_array($team->id, old('team_ids', $member?->teams->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="team-{{ $team->id }}">{{ $team->name }}</label>
                            </div>
                            @empty
                            <span class="text-muted small">No teams yet — create one under Team Management → Teams.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-success">
                {{ $mode === 'create' ? 'Create User' : 'Save Changes' }}
            </button>
            <a href="{{ route('team.members') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var select = document.getElementById('positionSelect');
        if (!select) return;
        select.addEventListener('change', function () {
            var opt = select.options[select.selectedIndex];
            var perms = [];
            try { perms = JSON.parse(opt.getAttribute('data-permissions') || '[]'); } catch (e) {}
            document.querySelectorAll('.perm-checkbox').forEach(function (cb) {
                cb.checked = perms.indexOf(cb.value) !== -1;
            });
        });
    }());
</script>
@endpush
@endsection
