@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0">Positions</h5>
            <span class="text-muted small">Positions are templates: their preset pre-fills the permission
                checkboxes when creating or editing a user.</span>
        </div>

        {{-- Create position --}}
        <form action="{{ route('team.positions.store') }}" method="POST" class="mb-5 border-bottom pb-4">
            @csrf
            <h6 class="mb-3">Create Position</h6>
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. DevOps Engineer"
                        value="{{ old('name') }}" required>
                </div>
                <div class="col-md-7">
                    <label class="form-label d-block">Permission preset</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($sectionsLabels as $key => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}"
                                id="create-perm-{{ $key }}" {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="create-perm-{{ $key }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </form>

        {{-- Positions table --}}
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Permission preset</th>
                    <th>Members</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($positions as $position)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $position->name }}</td>
                    <td>
                        @forelse ($position->permissions ?? [] as $perm)
                        <span class="badge bg-primary me-1">{{ $sectionsLabels[$perm] ?? $perm }}</span>
                        @empty
                        <span class="text-muted small">None</span>
                        @endforelse
                    </td>
                    <td>{{ $position->users_count }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                            data-bs-target="#editPosition{{ $position->id }}">Edit</button>
                        <form action="{{ route('team.positions.delete', $position) }}" method="POST" class="d-inline form-delete-position"
                            data-name="{{ $position->name }}">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-position">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No positions yet — create your first one above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Edit modals (placed outside the table so background and layout render correctly) --}}
        @foreach ($positions as $position)
        <div class="modal fade" id="editPosition{{ $position->id }}" tabindex="-1" aria-labelledby="editPositionLabel{{ $position->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form action="{{ route('team.positions.update', $position) }}" method="POST"
                    class="modal-content bg-secondary text-white border-0 shadow">
                    @csrf
                    <div class="modal-header border-bottom border-dark">
                        <h6 class="modal-title text-white" id="editPositionLabel{{ $position->id }}">Edit {{ $position->name }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label text-white">Name</label>
                        <input type="text" name="name" class="form-control mb-3 text-white bg-dark border-dark"
                            value="{{ old('name', $position->name) }}" required>
                        <label class="form-label d-block text-white">Permission preset</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($sectionsLabels as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                    value="{{ $key }}" id="edit{{ $position->id }}-perm-{{ $key }}"
                                    {{ in_array($key, old('permissions', $position->permissions ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label text-white"
                                    for="edit{{ $position->id }}-perm-{{ $key }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mt-2 mb-0">Changing this preset affects new users only —
                            existing members keep their own permissions.</p>
                    </div>
                    <div class="modal-footer border-top border-dark">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
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
        $('.btn-delete-position').on('click', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = form.data('name');

            Swal.fire({
                title: 'Delete Position?',
                text: 'Delete position ' + name + '? Members keep their accounts; this field is cleared.',
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
