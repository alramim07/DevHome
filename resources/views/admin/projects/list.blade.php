@extends('admin.layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0" id="projects-page-title">{{ ($currentScope ?? 'my') === 'all' ? 'All Projects' : 'My Projects' }}</h6>
                <button type="button" class="btn btn-danger btn-sm bulk-delete-projects-btn" style="display: none;">
                    <i class="fa fa-trash me-1"></i>Delete Selected (<span class="selected-projects-count">0</span>)
                </button>
                <button type="button" class="btn btn-primary btn-sm cancel-bulk-projects-btn" style="display: none;">
                    <i class="fa fa-times me-1"></i>Cancel
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('add.project') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i>Add New Project</a>
                @if(Auth::user()->isSuperAdmin())
                    <button type="button" id="btn-toggle-projects-scope" class="btn btn-outline-info btn-sm" data-scope="{{ ($currentScope ?? 'my') === 'all' ? 'my' : 'all' }}">
                        <i class="fa {{ ($currentScope ?? 'my') === 'all' ? 'fa-user' : 'fa-globe' }} me-1"></i>
                        <span id="btn-projects-scope-text">{{ ($currentScope ?? 'my') === 'all' ? 'Show My Projects' : 'See All Projects' }}</span>
                    </button>
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead style="text-align: center">
                    <tr class="text-white">              
                        <th scope="col" style="width: 40px;">
                            <input class="form-check-input select-all-projects" type="checkbox">
                        </th>
                        <th scope="col">Title</th>
                        <th scope="col">Date</th>
                        <th scope="col">Views</th>                        
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="projects-table-body">
                    @include('admin.projects.partials.table_rows', ['projects' => $projects, 'isAll' => ($currentScope ?? 'my') === 'all'])
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';

    function updateBulkDeleteState() {
        const checked = $('.project-checkbox:checked');
        const total = $('.project-checkbox').length;
        const count = checked.length;

        if (count > 0) {
            $('.selected-projects-count').text(count);
            $('.bulk-delete-projects-btn').fadeIn(150);
            $('.cancel-bulk-projects-btn').fadeIn(150);
        } else {
            $('.bulk-delete-projects-btn').fadeOut(150);
            $('.cancel-bulk-projects-btn').fadeOut(150);
        }

        if (total > 0 && count === total) {
            $('.select-all-projects').prop('checked', true).prop('indeterminate', false);
        } else if (count > 0 && count < total) {
            $('.select-all-projects').prop('checked', false).prop('indeterminate', true);
        } else {
            $('.select-all-projects').prop('checked', false).prop('indeterminate', false);
        }
    }

    function checkEmptyTable() {
        if ($('#projects-table-body tr').not('.no-projects-row').length === 0) {
            if ($('#projects-table-body .no-projects-row').length === 0) {
                $('#projects-table-body').html('<tr class="no-projects-row"><td colspan="5" class="text-center text-muted py-4">No projects found.</td></tr>');
            }
        }
    }

    // Checkbox change handlers
    $(document).on('change', '.project-checkbox', function() {
        updateBulkDeleteState();
    });

    $(document).on('change', '.select-all-projects', function() {
        const isChecked = $(this).is(':checked');
        $('.project-checkbox').prop('checked', isChecked);
        updateBulkDeleteState();
    });

    $(document).on('click', '.cancel-bulk-projects-btn', function() {
        $('.project-checkbox').prop('checked', false);
        $('.select-all-projects').prop('checked', false).prop('indeterminate', false);
        updateBulkDeleteState();
    });

    // Single Project Delete with SweetAlert2 & AJAX
    $(document).on('click', '.delete-project-btn', function(e) {
        e.preventDefault();
        const slug = $(this).data('slug');
        const title = $(this).data('title') || 'this project';
        const deleteUrl = "{{ route('projects.destroy', ':slug') }}".replace(':slug', slug);

        Swal.fire({
            title: 'Delete Project?',
            text: `Are you sure you want to delete "${title}"?`,
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
                    success: function(response) {
                        $('#project-row-' + slug).fadeOut(350, function() {
                            $(this).remove();
                            updateBulkDeleteState();
                            checkEmptyTable();
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || `"${title}" was deleted successfully.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete project.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    // Bulk Delete Projects with SweetAlert2 & AJAX
    $(document).on('click', '.bulk-delete-projects-btn', function(e) {
        e.preventDefault();
        const slugs = [];
        $('.project-checkbox:checked').each(function() {
            slugs.push($(this).val());
        });

        if (slugs.length === 0) return;

        Swal.fire({
            title: `Delete ${slugs.length} Project${slugs.length > 1 ? 's' : ''}?`,
            text: `Are you sure you want to delete the selected ${slugs.length} project(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('projects.bulk-delete') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        slugs: slugs
                    },
                    success: function(response) {
                        slugs.forEach(function(slug) {
                            $('#project-row-' + slug).fadeOut(350, function() {
                                $(this).remove();
                                updateBulkDeleteState();
                                checkEmptyTable();
                            });
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Projects deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete selected projects.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    @if(Auth::user()->isSuperAdmin())
    // Super Admin Scope Switcher
    const scopeBtn = document.getElementById('btn-toggle-projects-scope');
    const tableBody = document.getElementById('projects-table-body');
    const pageTitle = document.getElementById('projects-page-title');

    if (scopeBtn && tableBody) {
        scopeBtn.addEventListener('click', function() {
            const targetScope = scopeBtn.getAttribute('data-scope') || 'all';
            scopeBtn.disabled = true;
            scopeBtn.classList.add('opacity-75');

            fetch(`{{ route('list.project') }}?scope=${targetScope}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                tableBody.innerHTML = data.html;
                updateBulkDeleteState();
                if (data.scope === 'all') {
                    scopeBtn.setAttribute('data-scope', 'my');
                    scopeBtn.innerHTML = '<i class="fa fa-user me-1"></i><span id="btn-projects-scope-text">Show My Projects</span>';
                    if (pageTitle) pageTitle.textContent = 'All Projects';
                } else {
                    scopeBtn.setAttribute('data-scope', 'all');
                    scopeBtn.innerHTML = '<i class="fa fa-globe me-1"></i><span id="btn-projects-scope-text">See All Projects</span>';
                    if (pageTitle) pageTitle.textContent = 'My Projects';
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error!', 'Failed to load projects. Please try again.', 'error');
            })
            .finally(() => {
                scopeBtn.disabled = false;
                scopeBtn.classList.remove('opacity-75');
            });
        });
    }
    @endif
});
</script>
@endpush
@endsection
