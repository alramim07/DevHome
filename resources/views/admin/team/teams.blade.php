@extends('admin.layouts.app')
@section('content')
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
                        @forelse ($team->users as $member)
                        <form action="{{ route('team.teams.members.remove', [$team, $member->id]) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Remove {{ $member->name }} from {{ $team->name }}?')">
                            @csrf
                            <span class="badge bg-secondary me-1 mb-1">
                                {{ $member->name }}
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-1"
                                    title="Remove from team">×</button>
                            </span>
                        </form>
                        @empty
                        <span class="text-muted small">No members yet</span>
                        @endforelse
                    </td>
                    <td>
                        <form action="{{ route('team.teams.members.add', $team) }}" method="POST"
                            class="d-flex gap-1">
                            @csrf
                            <select name="user_id" class="form-select form-select-sm" required>
                                <option value="">Select member…</option>
                                @foreach ($teamCandidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-success">Add</button>
                        </form>
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
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.btn-delete-team').on('click', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = form.data('name');

            Swal.fire({
                title: 'Delete Team?',
                text: 'Delete team ' + name + '? Members stay in the system.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
