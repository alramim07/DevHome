@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded h-100 p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0" id="blogs-page-title">{{ ($currentScope ?? 'my') === 'all' ? 'All Blogs' : 'My Blogs' }}</h6>
                <button type="button" class="btn btn-danger btn-sm bulk-delete-blogs-btn" style="display: none;">
                    <i class="fa fa-trash me-1"></i>Delete Selected (<span class="selected-blogs-count">0</span>)
                </button>
                <button type="button" class="btn btn-primary btn-sm cancel-bulk-blogs-btn" style="display: none;">
                    <i class="fa fa-times me-1"></i>Cancel
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('blog.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i>Add Blog</a>
                @if(Auth::user()->isSuperAdmin())
                    <button type="button" id="btn-toggle-blogs-scope" class="btn btn-outline-info btn-sm" data-scope="{{ ($currentScope ?? 'my') === 'all' ? 'my' : 'all' }}">
                        <i class="fa {{ ($currentScope ?? 'my') === 'all' ? 'fa-user' : 'fa-globe' }} me-1"></i>
                        <span id="btn-blogs-scope-text">{{ ($currentScope ?? 'my') === 'all' ? 'Show My Blogs' : 'See All Blogs' }}</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input class="form-check-input select-all-blogs" type="checkbox">
                    </th>
                    <th>#</th>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="blogs-table-body">
                @include('admin.blog.partials.table_rows', ['blogs' => $blogs, 'isAll' => ($currentScope ?? 'my') === 'all'])
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
        const checked = $('.blog-checkbox:checked');
        const total = $('.blog-checkbox').length;
        const count = checked.length;

        if (count > 0) {
            $('.selected-blogs-count').text(count);
            $('.bulk-delete-blogs-btn').fadeIn(150);
            $('.cancel-bulk-blogs-btn').fadeIn(150);
        } else {
            $('.bulk-delete-blogs-btn').fadeOut(150);
            $('.cancel-bulk-blogs-btn').fadeOut(150);
        }

        if (total > 0 && count === total) {
            $('.select-all-blogs').prop('checked', true).prop('indeterminate', false);
        } else if (count > 0 && count < total) {
            $('.select-all-blogs').prop('checked', false).prop('indeterminate', true);
        } else {
            $('.select-all-blogs').prop('checked', false).prop('indeterminate', false);
        }
    }

    function checkEmptyTable() {
        if ($('#blogs-table-body tr').not('.no-blogs-row').length === 0) {
            if ($('#blogs-table-body .no-blogs-row').length === 0) {
                $('#blogs-table-body').html('<tr class="no-blogs-row"><td colspan="8" class="text-center text-muted py-4">No blog posts found.</td></tr>');
            }
        }
    }

    // Checkbox change handlers
    $(document).on('change', '.blog-checkbox', function() {
        updateBulkDeleteState();
    });

    $(document).on('change', '.select-all-blogs', function() {
        const isChecked = $(this).is(':checked');
        $('.blog-checkbox').prop('checked', isChecked);
        updateBulkDeleteState();
    });

    $(document).on('click', '.cancel-bulk-blogs-btn', function() {
        $('.blog-checkbox').prop('checked', false);
        $('.select-all-blogs').prop('checked', false).prop('indeterminate', false);
        updateBulkDeleteState();
    });

    // Single Blog Delete with SweetAlert2 & AJAX
    $(document).on('click', '.delete-blog-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const title = $(this).data('title') || 'this blog post';
        const deleteUrl = "{{ route('blog.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: 'Delete Blog Post?',
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
                        $('#blog-row-' + id).fadeOut(350, function() {
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
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete blog post.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    // Bulk Delete Blogs with SweetAlert2 & AJAX
    $(document).on('click', '.bulk-delete-blogs-btn', function(e) {
        e.preventDefault();
        const ids = [];
        $('.blog-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} Blog Post${ids.length > 1 ? 's' : ''}?`,
            text: `Are you sure you want to delete the selected ${ids.length} blog post(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('blog.bulk-delete') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        ids: ids
                    },
                    success: function(response) {
                        ids.forEach(function(id) {
                            $('#blog-row-' + id).fadeOut(350, function() {
                                $(this).remove();
                                updateBulkDeleteState();
                                checkEmptyTable();
                            });
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Blog posts deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Could not delete selected blog posts.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    @if(Auth::user()->isSuperAdmin())
    // Super Admin Scope Switcher
    const scopeBtn = document.getElementById('btn-toggle-blogs-scope');
    const tableBody = document.getElementById('blogs-table-body');
    const pageTitle = document.getElementById('blogs-page-title');

    if (scopeBtn && tableBody) {
        scopeBtn.addEventListener('click', function() {
            const targetScope = scopeBtn.getAttribute('data-scope') || 'all';
            scopeBtn.disabled = true;
            scopeBtn.classList.add('opacity-75');

            fetch(`{{ route('blog.list') }}?scope=${targetScope}`, {
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
                    scopeBtn.innerHTML = '<i class="fa fa-user me-1"></i><span id="btn-blogs-scope-text">Show My Blogs</span>';
                    if (pageTitle) pageTitle.textContent = 'All Blogs';
                } else {
                    scopeBtn.setAttribute('data-scope', 'all');
                    scopeBtn.innerHTML = '<i class="fa fa-globe me-1"></i><span id="btn-blogs-scope-text">See All Blogs</span>';
                    if (pageTitle) pageTitle.textContent = 'My Blogs';
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error!', 'Failed to load blog posts. Please try again.', 'error');
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
