@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0">All Members</h5>
            @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('team.members.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-user-plus me-1"></i>Create
                User</a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('team.members') }}" class="row g-2 align-items-end mb-4">
            <div class="col-md-3">
                <label class="form-label small">Position</label>
                <select name="position_id" class="form-select form-select-sm bg-dark">
                    <option value="">All positions</option>
                    @foreach ($positions as $position)
                    <option value="{{ $position->id }}"
                        {{ $filters['position_id'] === $position->id ? 'selected' : '' }}>{{ $position->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Team</label>
                <select name="team_id" class="form-select form-select-sm bg-dark">
                    <option value="">All teams</option>
                    @foreach ($teams as $team)
                    <option value="{{ $team->id }}" {{ $filters['team_id'] === $team->id ? 'selected' : '' }}>
                        {{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm bg-dark">
                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="deleted" {{ $filters['status'] === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary me-2">Apply filters</button>
                <a href="{{ route('team.members') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>

        {{-- Members table --}}
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Teams</th>
                        <th>Permissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                    <tr class="{{ $member->trashed() ? 'table-danger' : '' }}">
                        <td>
                            <img src="{{ $member->image ? media_url($member->image) : asset('images/default-user.png') }}"
                                class="rounded-circle" alt="" width="36" height="36" style="object-fit: cover;">
                        </td>
                        <td>
                            {{ $member->name }}
                            @if ($member->trashed())
                            <span class="badge bg-danger ms-1">Deleted</span>
                            @endif
                        </td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <span
                                class="badge bg-{{ ['super_admin' => 'danger', 'admin' => 'warning text-dark', 'member' => 'secondary'][$member->role] ?? 'secondary' }}">
                                {{ $rolesLabels[$member->role] ?? $member->role }}</span>
                        </td>
                        <td>{{ $member->position?->name ?? '—' }}</td>
                        <td>
                            @forelse ($member->teams as $team)
                            <span class="badge bg-secondary me-1">{{ $team->name }}</span>
                            @empty
                            <span class="text-muted small">—</span>
                            @endforelse
                        </td>
                        <td>
                            @if ($member->role === 'super_admin')
                            <span class="text-muted small">All sections</span>
                            @else
                            @forelse ($member->permissions ?? [] as $perm)
                            <span class="badge bg-primary me-1">{{ $sectionsLabels[$perm] ?? $perm }}</span>
                            @empty
                            <span class="text-muted small">None</span>
                            @endforelse
                            @endif
                        </td>
                        <td class="text-end">
                            @if ($member->trashed())
                            @if (auth()->user()->isSuperAdmin())
                            <form action="{{ route('team.members.restore', $member->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">Restore</button>
                            </form>
                            @endif
                            @else
                            @if (auth()->user()->isSuperAdmin())
                            <a href="{{ route('team.members.edit', $member->id) }}"
                                class="btn btn-sm btn-outline-info">Edit</a>
                            <form action="{{ route('team.members.delete', $member->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Delete {{ $member->name }}? Their data is kept and can be restored.')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No members match these filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
