<div class="h-100 bg-secondary rounded p-4 d-flex flex-column">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h6 class="mb-0">To Do List</h6>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-sm btn-outline-danger bulk-delete-btn" style="display: none; padding: 2px 8px; font-size: 0.75rem;">
        <i class="fa fa-trash me-1"></i>Delete (<span class="selected-count">0</span>)
      </button>
      <a href="{{ route('todo') }}">Show All</a>
    </div>
  </div>

  <form id="add-todo-form" class="mb-2">
    @csrf
    <div class="mb-2">
      <input name="task" id="task_input" class="form-control bg-dark border-0 mb-2" style="width: 65%; max-width: 280px;" type="text" placeholder="Enter task" required>
      <div class="d-flex">
        <input name="task_time" class="form-control bg-dark border-0 newwidth flatpickr-date" type="text"
          placeholder="Select date" required>
        <button type="submit" class="btn btn-primary ms-2">Add</button>
      </div>
    </div>
  </form>

  <div id="todo-container" class="flex-grow-1" style="max-height: 210px; overflow-y: auto; overflow-x: hidden; padding-right: 4px;">
    @forelse($tasks as $todo)
      <div class="d-flex align-items-center border-bottom py-2 todo-item" id="todo-{{ $todo->id }}">
        <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="{{ $todo->id }}" title="Select task">

        <div class="w-100 ms-3">
          <div class="d-flex w-100 align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              {{-- Status circle icon only (Green = Completed, Red = Pending) --}}
              <button type="button" class="btn btn-link p-0 me-2 toggle-dashboard-todo-status" data-id="{{ $todo->id }}"
                      title="{{ $todo->is_completed ? 'Completed (Click to change)' : 'Pending (Click to change)' }}"
                      style="line-height: 1; text-decoration: none;">
                <i class="fa fa-circle {{ $todo->is_completed ? 'text-success' : 'text-danger' }} todo-status-icon" style="font-size: 9px;"></i>
              </button>
              <span class="task-title-text {{ $todo->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                {{ $todo->task }}
                <br>
                <small class="text-muted" style="font-size: 0.75rem;">
                  <i class="fa fa-clock me-1"></i>{{ $todo->task_time ? $todo->task_time->format('d M, h:i A') : '' }}
                </small>
              </span>
            </div>
            <button class="btn btn-sm delete-todo" data-id="{{ $todo->id }}" title="Delete task">
              <i class="fa fa-times text-primary"></i>
            </button>
          </div>
        </div>
      </div>
    @empty
      <div id="no-tasks-msg" class="text-center text-muted small py-4">No tasks found.</div>
    @endforelse
  </div>
</div>

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      const csrfToken = '{{ csrf_token() }}';

      // Initialize Calendar Flatpickr for date picker
      flatpickr(".flatpickr-date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        theme: "dark",
        defaultDate: "today",
      });

      // Add Task AJAX
      $('#add-todo-form').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
          url: "{{ route('todo.store') }}",
          type: "POST",
          data: formData,
          success: function (response) {
            $('#task_input').val('');
            $('#no-tasks-msg').remove();

            // By default task is pending (Red circle icon)
            let newTask = `
              <div class="d-flex align-items-center border-bottom py-2 todo-item" id="todo-${response.id}">
                <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="${response.id}" title="Select task">
                <div class="w-100 ms-3">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <button type="button" class="btn btn-link p-0 me-2 toggle-dashboard-todo-status" data-id="${response.id}"
                              title="Pending (Click to change)" style="line-height: 1; text-decoration: none;">
                        <i class="fa fa-circle text-danger todo-status-icon" style="font-size: 9px;"></i>
                      </button>
                      <span class="task-title-text">
                        ${response.task} <br>
                        <small class="text-muted" style="font-size: 0.75rem;">
                          <i class="fa fa-clock me-1"></i>${response.date_formatted}
                        </small>
                      </span>
                    </div>
                    <button class="btn btn-sm delete-todo" data-id="${response.id}" title="Delete task">
                      <i class="fa fa-times text-primary"></i>
                    </button>
                  </div>
                </div>
              </div>`;

            $('#todo-container').prepend(newTask);
            updateBulkDeleteState();

            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Task added as Pending!',
              showConfirmButton: false,
              timer: 1500
            });
          },
          error: function () {
            Swal.fire('Error!', 'Could not add task.', 'error');
          }
        });
      });

      // Toggle Task Status on Dashboard (Clicking circle icon)
      $(document).on('click', '.toggle-dashboard-todo-status', function (e) {
        e.preventDefault();
        const btn = $(this);
        const id = btn.data('id');
        const icon = btn.find('.todo-status-icon');
        const taskText = btn.siblings('.task-title-text');

        $.ajax({
          url: "{{ route('todo.toggle', ':id') }}".replace(':id', id),
          type: "POST",
          data: {
            _token: csrfToken
          },
          success: function (response) {
            const isCompleted = response.is_completed;
            if (isCompleted) {
              icon.removeClass('text-danger').addClass('text-success');
              btn.attr('title', 'Completed (Click to change)');
              taskText.addClass('text-decoration-line-through text-muted');
            } else {
              icon.removeClass('text-success').addClass('text-danger');
              btn.attr('title', 'Pending (Click to change)');
              taskText.removeClass('text-decoration-line-through text-muted');
            }

            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: response.message || 'Status updated',
              showConfirmButton: false,
              timer: 1200
            });
          },
          error: function () {
            Swal.fire('Error!', 'Could not toggle status.', 'error');
          }
        });
      });

      // Bulk Delete State Tracker
      function updateBulkDeleteState() {
        let count = $('#todo-container .todo-item-checkbox:checked').length;

        if (count > 0) {
          $('.selected-count').text(count);
          $('.bulk-delete-btn').fadeIn(150);
        } else {
          $('.bulk-delete-btn').fadeOut(150);
        }
      }

      // Checkbox changes
      $(document).on('change', '.todo-item-checkbox', function () {
        updateBulkDeleteState();
      });

      // Bulk Delete Action
      $(document).on('click', '.bulk-delete-btn', function (e) {
        e.preventDefault();
        let ids = [];
        $('#todo-container .todo-item-checkbox:checked').each(function () {
          ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
          title: `Delete ${ids.length} Task${ids.length > 1 ? 's' : ''}?`,
          text: 'Are you sure you want to delete the selected tasks? This cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('todo.bulk-delete') }}",
              type: 'POST',
              data: {
                _token: csrfToken,
                ids: ids
              },
              success: function (response) {
                ids.forEach(function (id) {
                  $('#todo-' + id).fadeOut(300, function () {
                    $(this).remove();
                    updateBulkDeleteState();
                    if ($('#todo-container .todo-item').length === 0) {
                      $('#todo-container').html('<div id="no-tasks-msg" class="text-center text-muted small py-4">No tasks found.</div>');
                    }
                  });
                });
                Swal.fire('Deleted!', response.message, 'success');
              },
              error: function () {
                Swal.fire('Error!', 'Could not delete selected tasks.', 'error');
              }
            });
          }
        });
      });

      // Single Delete Action
      $(document).on('click', '.delete-todo', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let row = $('#todo-' + id);

        Swal.fire({
          title: 'Delete Task?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('todo.delete', ':id') }}".replace(':id', id),
              type: 'POST',
              data: {
                _token: csrfToken,
                _method: 'DELETE'
              },
              success: function () {
                row.fadeOut(300, function () {
                  $(this).remove();
                  updateBulkDeleteState();
                  if ($('#todo-container .todo-item').length === 0) {
                    $('#todo-container').html('<div id="no-tasks-msg" class="text-center text-muted small py-4">No tasks found.</div>');
                  }
                });
                Swal.fire('Deleted!', '', 'success');
              },
              error: function () {
                Swal.fire('Error!', 'Check console for details', 'error');
              }
            });
          }
        });
      });
    });
  </script>
@endpush
