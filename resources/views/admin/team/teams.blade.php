@extends('admin.layouts.app')
@section('content')
<style>
.candidate-styled-checkbox {
    border: 2px solid #70a9d4 !important;
    background-color: rgba(112, 169, 212, 0.12) !important;
    width: 1.25em !important;
    height: 1.25em !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
}
.candidate-styled-checkbox:hover {
    border-color: #8ec3ea !important;
    box-shadow: 0 0 6px rgba(112, 169, 212, 0.5) !important;
}
.candidate-styled-checkbox:checked {
    background-color: #70a9d4 !important;
    border-color: #70a9d4 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23191c24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3.2' d='M6 10l3 3l6-6'/%3e%3c/svg%3e") !important;
}
.candidate-styled-checkbox:indeterminate {
    background-color: #70a9d4 !important;
    border-color: #70a9d4 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23191c24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3.2' d='M6 10h8'/%3e%3c/svg%3e") !important;
}
.hover-candidate-item:hover {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0">Teams</h5>
            <span class="text-muted small">Department-style groups (e.g. Design Team, Frontend Team).</span>
        </div>

        {{-- Create team --}}
        <form action="{{ route('team.teams.store') }}" method="POST" class="mb-5 border-bottom pb-4">
            @csrf
            <h6 class="mb-3">Create Team</h6>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Team name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Design Team"
                        value="{{ old('name') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </form>

        {{-- Teams table --}}
        <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Members</th>
                    <th>Add member</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teams as $team)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <span class="fw-semibold">{{ $team->name }}</span>
                    </td>
                    <td>
                        <span class="fw-semibold" id="team-active-count-{{ $team->id }}">{{ $team->active_members_count }}</span>
                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 ms-2" style="font-size: 0.75rem; {{ $team->users->count() === 0 ? 'display: none;' : '' }}"
                            id="team-view-btn-{{ $team->id }}"
                            data-bs-toggle="modal" data-bs-target="#viewTeamMembers{{ $team->id }}" title="View and manage members">
                            View
                        </button>
                    </td>
                    <td>
                        @php
                            $teamMemberIds = $team->users->pluck('id')->toArray();
                            $availableCount = $teamCandidates->reject(fn($c) => in_array($c->id, $teamMemberIds))->count();
                        @endphp
                        <button type="button" class="btn btn-sm btn-outline-success text-nowrap"
                            data-bs-toggle="modal" data-bs-target="#addMembersModal{{ $team->id }}" title="Add members to team">
                            <i class="fa fa-user-plus me-1"></i>Add Members
                            <span class="badge bg-success ms-1" id="team-available-badge-{{ $team->id }}" style="font-size: 0.7rem; {{ $availableCount === 0 ? 'display: none;' : '' }}">{{ $availableCount }}</span>
                        </button>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                            data-bs-target="#editTeam{{ $team->id }}">Rename</button>
                        <form action="{{ route('team.teams.delete', $team) }}" method="POST" class="d-inline form-delete-team"
                            data-name="{{ $team->name }}">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-team">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No teams yet — create your first one above.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Rename modals (placed outside the table) --}}
        @foreach ($teams as $team)
        <div class="modal fade" id="editTeam{{ $team->id }}" tabindex="-1" aria-labelledby="editTeamLabel{{ $team->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('team.teams.update', $team) }}" method="POST" class="modal-content bg-secondary text-white border-0 shadow">
                    @csrf
                    <div class="modal-header border-bottom border-dark">
                        <h6 class="modal-title text-white" id="editTeamLabel{{ $team->id }}">Rename team</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label text-white">Team name</label>
                        <input type="text" name="name" class="form-control text-white bg-dark border-dark" value="{{ $team->name }}"
                            required>
                    </div>
                    <div class="modal-footer border-top border-dark">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- View / Manage Team Members modal --}}
        <div class="modal fade" id="viewTeamMembers{{ $team->id }}" tabindex="-1" aria-labelledby="viewTeamMembersLabel{{ $team->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-secondary text-white border-0 shadow">
                    <div class="modal-header border-bottom border-dark">
                        <h6 class="modal-title text-white" id="viewTeamMembersLabel{{ $team->id }}">
                            <i class="fa fa-users me-2 text-primary"></i>{{ $team->name }} — Members (<span class="modal-title-count">{{ $team->active_members_count }} active</span>)
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="team-members-list">
                            @foreach ($team->users as $member)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-dark team-member-row" id="team-member-row-{{ $team->id }}-{{ $member->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-medium">{{ $member->name }}</span>
                                    @if (method_exists($member, 'isSuspended') && $member->isSuspended())
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">Suspanded</span>
                                    @else
                                        <span class="badge bg-success" style="font-size: 0.7rem;">Active</span>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-member"
                                    style="font-size: 0.75rem;" title="Remove from team"
                                    data-team-id="{{ $team->id }}" data-team-name="{{ $team->name }}"
                                    data-user-id="{{ $member->id }}" data-user-name="{{ $member->name }}"
                                    data-url="{{ route('team.teams.members.remove', [$team, $member->id]) }}">
                                    <i class="fa fa-times me-1"></i>Remove
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center text-muted py-3 small no-team-members-msg" style="{{ $team->users->count() === 0 ? '' : 'display: none;' }}">
                            No members in this team yet.
                        </div>
                    </div>
                    <div class="modal-footer border-top border-dark">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Members Modal (with Search & Bulk Select) --}}
        <div class="modal fade add-members-modal" id="addMembersModal{{ $team->id }}" tabindex="-1" aria-labelledby="addMembersLabel{{ $team->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form action="{{ route('team.teams.members.add', $team) }}" method="POST"
                    class="modal-content bg-secondary text-white border-0 shadow form-add-team-members"
                    data-team-id="{{ $team->id }}" data-team-name="{{ $team->name }}">
                    @csrf
                    <div class="modal-header border-bottom border-dark">
                        <h6 class="modal-title text-white" id="addMembersLabel{{ $team->id }}">
                            <i class="fa fa-user-plus me-2 text-success"></i>Add Members to {{ $team->name }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $teamCurrentIds = $team->users->pluck('id')->toArray();
                            $availableCandidates = $teamCandidates->reject(fn($c) => in_array($c->id, $teamCurrentIds));
                        @endphp

                        <div class="all-added-msg text-center text-muted py-4" style="{{ $availableCandidates->isEmpty() ? '' : 'display: none;' }}">
                            <i class="fa fa-info-circle fa-2x mb-2 d-block text-secondary"></i>
                            All active members are already in this team.
                        </div>

                        <div class="available-candidates-section" style="{{ $availableCandidates->isEmpty() ? 'display: none;' : '' }}">
                            {{-- Search bar & Bulk Select Header --}}
                            <div class="row g-2 align-items-center mb-3 pb-3 border-bottom border-dark">
                                <div class="col-md-7">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-dark border-dark text-muted">
                                            <i class="fa fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control bg-dark text-white border-dark search-candidate-input"
                                            placeholder="Search by name, email, or position...">
                                    </div>
                                </div>
                                <div class="col-md-5 d-flex align-items-center justify-content-between justify-content-md-end gap-2">
                                    <div class="form-check m-0 d-flex align-items-center">
                                        <input class="form-check-input select-all-candidates candidate-styled-checkbox" type="checkbox" id="selectAllCandidates{{ $team->id }}">
                                        <label class="form-check-label ms-2 small text-muted user-select-none" for="selectAllCandidates{{ $team->id }}" style="cursor: pointer;">
                                            Select All (<span class="visible-candidates-count">{{ $availableCandidates->count() }}</span>)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Scrollable Candidates List --}}
                            <div class="candidates-container pe-1" style="max-height: 320px; overflow-y: auto;">
                                @foreach ($availableCandidates as $candidate)
                                <div class="d-flex align-items-center justify-content-between p-2 rounded mb-1 candidate-item hover-candidate-item"
                                     id="cand-row-{{ $team->id }}-{{ $candidate->id }}"
                                     data-name="{{ strtolower($candidate->name) }}"
                                     data-email="{{ strtolower($candidate->email) }}"
                                     data-position="{{ strtolower($candidate->position?->name ?? '') }}"
                                     style="transition: background-color 0.15s ease;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="checkbox" name="user_ids[]" value="{{ $candidate->id }}"
                                               class="form-check-input candidate-checkbox candidate-styled-checkbox m-0"
                                               id="cand-{{ $team->id }}-{{ $candidate->id }}">
                                        <img src="{{ $candidate->image ? media_url($candidate->image) : asset('images/default-user.png') }}"
                                             class="rounded-circle" width="36" height="36" style="object-fit: cover;" alt="">
                                        <div>
                                            <label class="fw-semibold text-white mb-0 user-select-none d-block" for="cand-{{ $team->id }}-{{ $candidate->id }}" style="cursor: pointer;">
                                                {{ $candidate->name }}
                                            </label>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $candidate->email }}
                                                @if ($candidate->position)
                                                <span class="badge bg-dark text-info ms-1">{{ $candidate->position->name }}</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.7rem;">Active</span>
                                </div>
                                @endforeach
                                <div class="no-candidate-match text-center text-muted py-4 small" style="display: none;">
                                    No members match your search.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-dark d-flex justify-content-between">
                        <span class="small text-muted">
                            Selected: <strong class="text-white selected-count-display">0</strong>
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm btn-submit-candidates" disabled style="{{ $availableCandidates->isEmpty() ? 'display: none;' : '' }}">
                                <i class="fa fa-plus me-1"></i>Add Selected (<span class="selected-count-display">0</span>)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const csrfToken = '{{ csrf_token() }}';

        // Delete team confirmation
        $('.btn-delete-team').on('click', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = form.data('name');

            Swal.fire({
                title: 'Delete Team?',
                text: 'Delete team ' + name + '? Members stay in the system.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Search candidates filter
        $(document).on('input', '.search-candidate-input', function () {
            const modal = $(this).closest('.add-members-modal');
            const query = $(this).val().toLowerCase().trim();
            let visibleCount = 0;

            modal.find('.candidate-item').each(function () {
                const name = String($(this).data('name') || '');
                const email = String($(this).data('email') || '');
                const position = String($(this).data('position') || '');

                if (!query || name.includes(query) || email.includes(query) || position.includes(query)) {
                    $(this).removeClass('d-none');
                    visibleCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            modal.find('.visible-candidates-count').text(visibleCount);
            if (visibleCount === 0) {
                modal.find('.no-candidate-match').show();
            } else {
                modal.find('.no-candidate-match').hide();
            }

            updateCandidateSelectionState(modal);
        });

        // Select All toggle for visible candidates
        $(document).on('change', '.select-all-candidates', function () {
            const modal = $(this).closest('.add-members-modal');
            const isChecked = $(this).is(':checked');
            modal.find('.candidate-item:not(.d-none) .candidate-checkbox').prop('checked', isChecked);
            updateCandidateSelectionState(modal);
        });

        // Individual candidate checkbox toggle
        $(document).on('change', '.candidate-checkbox', function () {
            const modal = $(this).closest('.add-members-modal');
            updateCandidateSelectionState(modal);
        });

        function updateCandidateSelectionState(modal) {
            const totalVisible = modal.find('.candidate-item:not(.d-none) .candidate-checkbox').length;
            const selectedVisible = modal.find('.candidate-item:not(.d-none) .candidate-checkbox:checked').length;
            const totalSelected = modal.find('.candidate-checkbox:checked').length;

            modal.find('.selected-count-display').text(totalSelected);

            const selectAllCheckbox = modal.find('.select-all-candidates');
            if (totalVisible > 0 && selectedVisible === totalVisible) {
                selectAllCheckbox.prop('checked', true).prop('indeterminate', false);
            } else if (selectedVisible > 0 && selectedVisible < totalVisible) {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', true);
            } else {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            }

            const submitBtn = modal.find('.btn-submit-candidates');
            submitBtn.prop('disabled', totalSelected === 0);
        }

        // Reset modal state on open
        $('.add-members-modal').on('show.bs.modal', function () {
            const modal = $(this);
            modal.find('.search-candidate-input').val('');
            modal.find('.candidate-item').removeClass('d-none');
            modal.find('.no-candidate-match').hide();
            modal.find('.visible-candidates-count').text(modal.find('.candidate-item').length);
            modal.find('.candidate-checkbox, .select-all-candidates').prop('checked', false).prop('indeterminate', false);
            updateCandidateSelectionState(modal);
        });

        // AJAX Add Members form submission without page reload
        $(document).on('submit', '.form-add-team-members', function (e) {
            e.preventDefault();
            const form = $(this);
            const modal = form.closest('.add-members-modal');
            const teamId = form.data('team-id');
            const submitBtn = form.find('.btn-submit-candidates');
            const originalBtnHtml = submitBtn.html();
            const formData = form.serialize();

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Adding...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (response) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);

                    // Close modal safely
                    const modalEl = modal[0];
                    const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    bsModal.hide();

                    // Update active count in table
                    $('#team-active-count-' + teamId).text(response.active_members_count);

                    // Ensure "View" button is visible in table
                    $('#team-view-btn-' + teamId).show();

                    // Update View modal
                    const viewModal = $('#viewTeamMembers' + teamId);
                    viewModal.find('.modal-title-count').text(response.active_members_count + ' active');
                    viewModal.find('.no-team-members-msg').hide();

                    // For each newly added user:
                    if (response.added_users && response.added_users.length > 0) {
                        response.added_users.forEach(function (user) {
                            // 1. Remove from Add Members candidate list
                            modal.find('#cand-row-' + teamId + '-' + user.id).remove();

                            // 2. Prepend to View Members modal list if not already there
                            if ($('#team-member-row-' + teamId + '-' + user.id).length === 0) {
                                const removeUrl = "{{ route('team.teams.members.remove', [':teamId', ':userId']) }}"
                                    .replace(':teamId', teamId)
                                    .replace(':userId', user.id);

                                const statusBadge = user.is_suspended 
                                    ? '<span class="badge bg-danger" style="font-size: 0.7rem;">Suspanded</span>' 
                                    : '<span class="badge bg-success" style="font-size: 0.7rem;">Active</span>';

                                const newRow = `
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-dark team-member-row" id="team-member-row-${teamId}-${user.id}">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-medium">${user.name}</span>
                                            ${statusBadge}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-member"
                                            style="font-size: 0.75rem;" title="Remove from team"
                                            data-team-id="${teamId}" data-team-name="${response.team_name}"
                                            data-user-id="${user.id}" data-user-name="${user.name}"
                                            data-url="${removeUrl}">
                                            <i class="fa fa-times me-1"></i>Remove
                                        </button>
                                    </div>
                                `;
                                viewModal.find('.team-members-list').prepend(newRow);
                            }
                        });
                    }

                    // Update available count badge on Add Members button
                    const remainingCandidates = modal.find('.candidate-item').length;
                    const availableBadge = $('#team-available-badge-' + teamId);
                    if (remainingCandidates > 0) {
                        availableBadge.text(remainingCandidates).show();
                    } else {
                        availableBadge.hide();
                        modal.find('.available-candidates-section').hide();
                        modal.find('.all-added-msg').show();
                        modal.find('.btn-submit-candidates').hide();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    const errorMsg = xhr.responseJSON?.message || 'Could not add members to team.';
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        });

        // AJAX Remove Member with SweetAlert2 confirmation
        $(document).on('click', '.btn-remove-member', function (e) {
            e.preventDefault();
            const btn = $(this);
            const teamId = btn.data('team-id');
            const teamName = btn.data('team-name') || 'this team';
            const userId = btn.data('user-id');
            const userName = btn.data('user-name') || 'this member';
            const removeUrl = btn.data('url');

            Swal.fire({
                title: 'Remove Member?',
                text: `Are you sure you want to remove ${userName} from ${teamName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: removeUrl,
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function (response) {
                            // Remove row from View Members modal
                            const row = $('#team-member-row-' + teamId + '-' + userId);
                            row.fadeOut(300, function () {
                                $(this).remove();
                                const viewModal = $('#viewTeamMembers' + teamId);
                                if (viewModal.find('.team-member-row').length === 0) {
                                    viewModal.find('.no-team-members-msg').show();
                                    $('#team-view-btn-' + teamId).hide();
                                }
                            });

                            // Update active count in table
                            $('#team-active-count-' + teamId).text(response.active_members_count);

                            // Update View modal title count
                            $('#viewTeamMembers' + teamId).find('.modal-title-count').text(response.active_members_count + ' active');

                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: response.message || `${userName} was removed from ${teamName}.`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function (xhr) {
                            const errorMsg = xhr.responseJSON?.message || 'Could not remove member from team.';
                            Swal.fire('Error!', errorMsg, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
