@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded h-100 p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h6 class="mb-0" id="blogs-page-title">{{ ($currentScope ?? 'my') === 'all' ? 'All Blogs' : 'My Blogs' }}</h6>
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

@if(Auth::user()->isSuperAdmin())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-toggle-blogs-scope');
    const tableBody = document.getElementById('blogs-table-body');
    const pageTitle = document.getElementById('blogs-page-title');
    if (!btn || !tableBody) return;

    btn.addEventListener('click', function() {
        const targetScope = btn.getAttribute('data-scope') || 'all';
        btn.disabled = true;
        btn.classList.add('opacity-75');

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
            if (data.scope === 'all') {
                btn.setAttribute('data-scope', 'my');
                btn.innerHTML = '<i class="fa fa-user me-1"></i><span id="btn-blogs-scope-text">Show My Blogs</span>';
                if (pageTitle) pageTitle.textContent = 'All Blogs';
            } else {
                btn.setAttribute('data-scope', 'all');
                btn.innerHTML = '<i class="fa fa-globe me-1"></i><span id="btn-blogs-scope-text">See All Blogs</span>';
                if (pageTitle) pageTitle.textContent = 'My Blogs';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load blog posts. Please try again.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-75');
        });
    });
});
</script>
@endpush
@endif
@endsection
