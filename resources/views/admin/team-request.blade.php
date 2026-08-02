@extends('admin.layouts.app')
@section('content')
@push('scripts')
  <script>
    $(document).ready(function() {
      $('.delete-request').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let row = $('#request-' + id);

        Swal.fire({
          title: 'Delete Request?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('join.delete', ':id') }}".replace(':id', id),
              type: 'POST',
              data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
              },
              success: function(response) {
                row.fadeOut(400, function() { $(this).remove(); });
                Swal.fire('Deleted!', '', 'success');
              },
              error: function(xhr) {
                console.log(xhr.responseText);
                Swal.fire('Error!', 'Check console for details', 'error');
              }
            });
          }
        });
      });
    });
  </script>
@endpush
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary rounded h-100 p-4">
    <table class="table">
      <h2 class="mb-4">Team Join Requests</h2>
        <thead>
        <tr>          
          <th scope="col">No.</th>
          <th scope="col">Name</th>
          <th scope="col">Portfolio/Github Link</th>
          <th scope="col">Date</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($requests as $request)
        <tr id="request-{{ $request->id }}">
          <th scope="row">{{ $loop->iteration }}</th>
          <td>{{ $request->name }}</td>
          <td>{{ $request->link }}</td>
          <td>{{ $request->created_at->diffForHumans() }}</td>
          <td>
            <button class="btn btn-sm delete-request" data-id="{{ $request->id }}">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection