@extends('admin.layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h6 class="mb-0" id="projects-page-title">{{ ($currentScope ?? 'my') === 'all' ? 'All Projects' : 'My Projects' }}</h6>
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

@if(Auth::user()->isSuperAdmin())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-toggle-projects-scope');
    const tableBody = document.getElementById('projects-table-body');
    const pageTitle = document.getElementById('projects-page-title');
    if (!btn || !tableBody) return;

    btn.addEventListener('click', function() {
        const targetScope = btn.getAttribute('data-scope') || 'all';
        btn.disabled = true;
        btn.classList.add('opacity-75');

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
            if (data.scope === 'all') {
                btn.setAttribute('data-scope', 'my');
                btn.innerHTML = '<i class="fa fa-user me-1"></i><span id="btn-projects-scope-text">Show My Projects</span>';
                if (pageTitle) pageTitle.textContent = 'All Projects';
            } else {
                btn.setAttribute('data-scope', 'all');
                btn.innerHTML = '<i class="fa fa-globe me-1"></i><span id="btn-projects-scope-text">See All Projects</span>';
                if (pageTitle) pageTitle.textContent = 'My Projects';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load projects. Please try again.');
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