@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary rounded h-100 p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <h6 class="mb-0" id="emails-page-title">{{ ($currentScope ?? 'my') === 'all' ? 'All Emails' : 'My Emails' }}</h6>
      @if(Auth::user()->isSuperAdmin())
        <button type="button" id="btn-toggle-emails-scope" class="btn btn-outline-info btn-sm" data-scope="{{ ($currentScope ?? 'my') === 'all' ? 'my' : 'all' }}">
          <i class="fa {{ ($currentScope ?? 'my') === 'all' ? 'fa-user' : 'fa-globe' }} me-1"></i>
          <span id="btn-emails-scope-text">{{ ($currentScope ?? 'my') === 'all' ? 'Show My Emails' : 'See All Emails' }}</span>
        </button>
      @endif
    </div>
    <div class="table-responsive">
      <table class="table table-dark table-striped align-middle">
        <thead>
        <tr>
          <th scope="col">No.</th>
          <th scope="col">Name</th>
          <th scope="col">Email Address</th>
          <th scope="col">Message</th>
          <th scope="col">Date</th>
        </tr>
      </thead>
      <tbody id="emails-table-body">
        @include('admin.partials.email_table_rows', ['emails' => $emails, 'isAll' => ($currentScope ?? 'my') === 'all'])
      </tbody>
      </table>
    </div>
  </div>
</div>

@if(Auth::user()->isSuperAdmin())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-toggle-emails-scope');
    const tableBody = document.getElementById('emails-table-body');
    const pageTitle = document.getElementById('emails-page-title');
    if (!btn || !tableBody) return;

    btn.addEventListener('click', function() {
        const targetScope = btn.getAttribute('data-scope') || 'all';
        btn.disabled = true;
        btn.classList.add('opacity-75');

        fetch(`{{ route('email.list') }}?scope=${targetScope}`, {
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
                btn.innerHTML = '<i class="fa fa-user me-1"></i><span id="btn-emails-scope-text">Show My Emails</span>';
                if (pageTitle) pageTitle.textContent = 'All Emails';
            } else {
                btn.setAttribute('data-scope', 'all');
                btn.innerHTML = '<i class="fa fa-globe me-1"></i><span id="btn-emails-scope-text">See All Emails</span>';
                if (pageTitle) pageTitle.textContent = 'My Emails';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load emails. Please try again.');
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
