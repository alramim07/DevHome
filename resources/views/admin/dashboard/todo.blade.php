

        <div class="h-100 bg-secondary rounded p-4">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">To Do List</h6>
            <div class="d-flex align-items-center gap-2">
              <div class="form-check m-0 d-flex align-items-center" style="font-size: 0.85rem;">
                <input class="form-check-input select-all-todos" type="checkbox" id="selectAllTodosWidget" title="Select All">
                <label class="form-check-label ms-1 text-muted" for="selectAllTodosWidget" style="cursor: pointer; user-select: none;">All</label>
              </div>
              <button type="button" class="btn btn-sm btn-outline-danger bulk-delete-btn" style="display: none; padding: 2px 8px; font-size: 0.75rem;">
                <i class="fa fa-trash me-1"></i>Delete (<span class="selected-count">0</span>)
              </button>
            </div>
          </div>

          <form id="add-todo-form"> @csrf

            <div class="mb-2">
              <input name="task" id="task_input" class="form-control bg-dark border-0 mb-2" type="text" placeholder="Enter task" required>
              <div class="d-flex">
                <input name="task_time" class="form-control bg-dark border-0 newwidth flatpickr-date" type="text"
                  placeholder="Select date" required>
                <button type="submit" class="btn btn-primary ms-2">Add</button>
              </div>
            </div>
          </form>

          <div id="todo-container">
            @foreach($tasks as $todo)
              <div class="d-flex align-items-center border-bottom py-2" id="todo-{{ $todo->id }}">
                <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="{{ $todo->id }}" title="Select task">

                <div class="w-100 ms-3">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <span class="{{ $todo->is_completed ? 'text-decoration-line-through' : '' }}">
                      {{ $todo->task }}
                      <br>
                      <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="fa fa-clock me-1"></i>{{ $todo->task_time->format('d M, h:i A') }}
                      </small>
                    </span>
                    <button class="btn btn-sm delete-todo" data-id="{{ $todo->id }}" title="Delete task">
                      <i class="fa fa-times text-primary"></i>
                    </button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

        </div>

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#add-todo-form').on('submit', function (e) {
        e.preventDefault(); // This stops the page from reloading

        let formData = $(this).serialize(); // Gathers task and task_time

        $.ajax({
          url: "{{ route('todo.store') }}",
          type: "POST",
          data: formData,
          success: function (response) {
            // 1. Clear the inputs
            $('#task_input').val('');

            // 2. Append the new task to the list dynamically
            // Note: We use response data sent back from the controller
            let newTask = `
                  <div class="d-flex align-items-center border-bottom py-2" id="todo-${response.id}">
                      <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="${response.id}" title="Select task">
                      <div class="w-100 ms-3">
                          <div class="d-flex w-100 align-items-center justify-content-between">
                              <span>
                                  ${response.task} <br>
                                  <small class="text-muted" style="font-size: 0.75rem;">
                                      <i class="fa fa-clock me-1"></i>${response.date_formatted}
                                  </small>
                              </span>
                              <button class="btn btn-sm delete-todo" data-id="${response.id}" title="Delete task">
                                  <i class="fa fa-times text-primary"></i>
                              </button>
                          </div>
                      </div>
                  </div>`;

            $('#todo-container').prepend(newTask); // Adds the new task to the top
            updateBulkDeleteState();

            // 3. Show success toast
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Task added!',
              showConfirmButton: false,
              timer: 1500
            });
          },
          error: function () {
            Swal.fire('Error!', 'Could not add task.', 'error');
          }
        });
      });
      // 1. Initialize Calendar (No Time, just Date)
      flatpickr(".flatpickr-date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        theme: "dark",
        defaultDate: "today",
      });

      // Bulk Delete State Tracker
      function updateBulkDeleteState() {
        let selected = $('.todo-item-checkbox:checked');
        let total = $('.todo-item-checkbox').length;
        let count = selected.length;

        if (count > 0) {
          $('.selected-count').text(count);
          $('.bulk-delete-btn').fadeIn(150);
        } else {
          $('.bulk-delete-btn').fadeOut(150);
        }

        if (total > 0 && count === total) {
          $('.select-all-todos').prop('checked', true).prop('indeterminate', false);
        } else if (count > 0 && count < total) {
          $('.select-all-todos').prop('checked', false).prop('indeterminate', true);
        } else {
          $('.select-all-todos').prop('checked', false).prop('indeterminate', false);
        }
      }

      // Checkbox changes
      $(document).on('change', '.todo-item-checkbox', function () {
        updateBulkDeleteState();
      });

      // Select All toggle
      $(document).on('change', '.select-all-todos', function () {
        let isChecked = $(this).is(':checked');
        $('.todo-item-checkbox').prop('checked', isChecked);
        updateBulkDeleteState();
      });

      // Bulk Delete Action
      $(document).on('click', '.bulk-delete-btn', function (e) {
        e.preventDefault();
        let ids = [];
        $('.todo-item-checkbox:checked').each(function () {
          ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
          title: `Delete ${ids.length} Task${ids.length > 1 ? 's' : ''}?`,
          text: 'Are you sure you want to delete the selected tasks? This cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('todo.bulk-delete') }}",
              type: 'POST',
              data: {
                _token: '{{ csrf_token() }}',
                ids: ids
              },
              success: function (response) {
                ids.forEach(function (id) {
                  $('#todo-' + id).fadeOut(300, function () {
                    $(this).remove();
                    updateBulkDeleteState();
                  });
                });
                Swal.fire('Deleted!', response.message, 'success');
              },
              error: function (xhr) {
                console.log(xhr.responseText);
                Swal.fire('Error!', 'Could not delete selected tasks.', 'error');
              }
            });
          }
        });
      });

      // 2. Single Delete Logic (Delegated to document so dynamically added tasks work immediately)
      $(document).on('click', '.delete-todo', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let row = $('#todo-' + id);

        Swal.fire({
          title: 'Delete Task?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('todo.delete', ':id') }}".replace(':id', id),
              type: 'POST', // We use POST but send DELETE inside data
              data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
              },
              success: function (response) {
                row.fadeOut(400, function () {
                  $(this).remove();
                  updateBulkDeleteState();
                });
                Swal.fire('Deleted!', '', 'success');
              },
              error: function (xhr) {
                // This logs the ACTUAL error to the console (Press F12 to see it)
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