@extends('admin.layouts.app')
@section('content')
<style>
.select-all-members,
.member-checkbox {
    border: 2px solid #70a9d4 !important;
    background-color: rgba(112, 169, 212, 0.12) !important;
    width: 1.25em !important;
    height: 1.25em !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
}
.select-all-members:hover,
.member-checkbox:hover {
    border-color: #8ec3ea !important;
    box-shadow: 0 0 6px rgba(112, 169, 212, 0.5) !important;
}
.select-all-members:checked,
.member-checkbox:checked {
    background-color: #70a9d4 !important;
    border-color: #70a9d4 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23191c24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3.2' d='M6 10l3 3l6-6'/%3e%3c/svg%3e") !important;
}
.select-all-members:indeterminate {
    background-color: #70a9d4 !important;
    border-color: #70a9d4 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23191c24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3.2' d='M6 10h8'/%3e%3c/svg%3e") !important;
}
.select-all-members:focus,
.member-checkbox:focus {
    border-color: #70a9d4 !important;
    box-shadow: 0 0 0 0.25rem rgba(112, 169, 212, 0.3) !important;
}
</style>
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0">All Members</h5>
                @if (auth()->user()->isSuperAdmin())
                <button type="button" class="btn btn-sm btn-danger bulk-delete-members-btn" style="display: none;">
                    <i class="fa fa-trash me-1"></i>Delete (<span class="selected-count">0</span>)
                </button>
                @endif
            </div>
            @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('team.members.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-user-plus me-1"></i>Create User
            </a>
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
                    <option value="active" {{ in_array($filters['status'], ['active', '']) ? 'selected' : '' }}>All Members</option>
                    <option value="active_only" {{ $filters['status'] === 'active_only' ? 'selected' : '' }}>Active only</option>
                    <option value="suspended" {{ in_array($filters['status'], ['suspended', 'suspanded']) ? 'selected' : '' }}>Suspanded only</option>
                    <option value="deleted" {{ $filters['status'] === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All (incl. deleted)</option>
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
                        @if (auth()->user()->isSuperAdmin())
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" class="form-check-input select-all-members" title="Select All">
                        </th>
                        @endif
                        <th style="width: 50px;">Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Teams</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                    <tr id="member-row-{{ $member->id }}" class="{{ $member->trashed() ? 'table-danger' : '' }}">
                        @if (auth()->user()->isSuperAdmin())
                        <td class="text-center">
                            @if (!$member->trashed() && $member->id !== auth()->id() && !$member->isSuperAdmin())
                            <input type="checkbox" class="form-check-input member-checkbox" value="{{ $member->id }}">
                            @endif
                        </td>
                        @endif
                        <td>
                            <img src="{{ $member->image ? media_url($member->image) : asset('images/default-user.png') }}"
                                class="rounded-circle" alt="" width="36" height="36" style="object-fit: cover;">
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $member->name }}</span>
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
                            @if ($member->trashed())
                                <span class="badge bg-secondary">Deleted</span>
                            @elseif ($member->isSuperAdmin() || $member->id === auth()->id() || !auth()->user()->isSuperAdmin())
                                @if ($member->isSuspended())
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="fa fa-circle text-danger me-1" style="font-size: 8px;"></i>Suspanded
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="fa fa-circle text-success me-1" style="font-size: 8px;"></i>Active
                                    </span>
                                @endif
                            @else
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-{{ $member->isSuspended() ? 'danger' : 'success' }} dropdown-toggle status-dropdown-btn py-1 px-2"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" id="status-dropdown-{{ $member->id }}">
                                        <i class="fa fa-circle {{ $member->isSuspended() ? 'text-danger' : 'text-success' }} me-1 status-dot" style="font-size: 8px;"></i>
                                        <span class="status-text">{{ $member->isSuspended() ? 'Suspanded' : 'Active' }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="status-dropdown-{{ $member->id }}">
                                        <li>
                                            <a class="dropdown-item change-status-btn {{ !$member->isSuspended() ? 'active' : '' }}"
                                               href="javascript:void(0)" data-id="{{ $member->id }}" data-status="active">
                                                <i class="fa fa-circle text-success me-2" style="font-size: 8px;"></i>Active
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item change-status-btn {{ $member->isSuspended() ? 'active' : '' }}"
                                               href="javascript:void(0)" data-id="{{ $member->id }}" data-status="suspended">
                                                <i class="fa fa-circle text-danger me-2" style="font-size: 8px;"></i>Suspanded
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
                                @if ($member->id !== auth()->id() && !$member->isSuperAdmin())
                                <button type="button" class="btn btn-sm btn-outline-danger delete-member-btn"
                                    data-id="{{ $member->id }}" data-name="{{ $member->name }}">Delete</button>
                                @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isSuperAdmin() ? 9 : 8 }}" class="text-center text-muted py-4">No members match these filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const csrfToken = '{{ csrf_token() }}';

    // Bulk Delete State Manager
    function updateBulkDeleteState() {
        const selected = $('.member-checkbox:checked');
        const total = $('.member-checkbox').length;
        const count = selected.length;

        if (count > 0) {
            $('.selected-count').text(count);
            $('.bulk-delete-members-btn').fadeIn(150);
        } else {
            $('.bulk-delete-members-btn').fadeOut(150);
        }

        if (total > 0 && count === total) {
            $('.select-all-members').prop('checked', true).prop('indeterminate', false);
        } else if (count > 0 && count < total) {
            $('.select-all-members').prop('checked', false).prop('indeterminate', true);
        } else {
            $('.select-all-members').prop('checked', false).prop('indeterminate', false);
        }
    }

    // Single Checkbox Click
    $(document).on('change', '.member-checkbox', function () {
        updateBulkDeleteState();
    });

    // Select All Click
    $(document).on('change', '.select-all-members', function () {
        const isChecked = $(this).is(':checked');
        $('.member-checkbox').prop('checked', isChecked);
        updateBulkDeleteState();
    });

    // Single Member Delete with SweetAlert2 & AJAX
    $(document).on('click', '.delete-member-btn', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name') || 'this member';
        const deleteUrl = "{{ route('team.members.delete', ':id') }}".replace(':id', id);

        Swal.fire({
            title: 'Delete Member?',
            text: `Are you sure you want to delete ${name}? Their data is kept and can be restored.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function (response) {
                        $('#member-row-' + id).fadeOut(350, function () {
                            $(this).remove();
                            updateBulkDeleteState();
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || `${name} was deleted successfully.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete member.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    // Bulk Delete Action with SweetAlert2 & AJAX
    $(document).on('click', '.bulk-delete-members-btn', function (e) {
        e.preventDefault();
        const ids = [];
        $('.member-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} Member${ids.length > 1 ? 's' : ''}?`,
            text: `Are you sure you want to delete the selected ${ids.length} member(s)? Their data can be restored later.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('team.members.bulk-delete') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        ids: ids
                    },
                    success: function (response) {
                        ids.forEach(function (id) {
                            $('#member-row-' + id).fadeOut(350, function () {
                                $(this).remove();
                                updateBulkDeleteState();
                            });
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Members deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete selected members.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    // Change Status Dropdown Action with AJAX
    $(document).on('click', '.change-status-btn', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        const statusUrl = "{{ route('team.members.status', ':id') }}".replace(':id', id);
        const dropdownBtn = $('#status-dropdown-' + id);
        const menu = dropdownBtn.siblings('.dropdown-menu');

        $.ajax({
            url: statusUrl,
            type: 'POST',
            data: {
                _token: csrfToken,
                status: status
            },
            success: function (response) {
                const isSuspended = (response.status === 'suspended');
                const label = response.status_label || (isSuspended ? 'Suspanded' : 'Active');

                // Update dropdown button appearance
                dropdownBtn.removeClass('btn-outline-success btn-outline-danger')
                           .addClass(isSuspended ? 'btn-outline-danger' : 'btn-outline-success');
                dropdownBtn.find('.status-dot')
                           .removeClass('text-success text-danger')
                           .addClass(isSuspended ? 'text-danger' : 'text-success');
                dropdownBtn.find('.status-text').text(label);

                // Update active states in dropdown menu
                menu.find('.change-status-btn').removeClass('active');
                menu.find(`[data-status="${response.status}"]`).addClass('active');

                // Toast notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message || `Status updated to ${label}`,
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Could not update status.';
                Swal.fire('Error!', errorMsg, 'error');
            }
        });
    });
});
</script>
@endpush