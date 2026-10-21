

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
              <input name="task" id="task_input" class="form-control bg-dark border-0 mb-2" style="width: 65%; max-width: 280px;" type="text" placeholder="Enter task" required>
              <div class="d-flex">
                <input name="task_time" class="form-control bg-dark border-0 newwidth flatpickr-date" type="text"
                  placeholder="Select date" required>
                <button type="submit" class="btn btn-primary ms-2">Add</button>
              </div>
            </div>
          </form>

          <!-- Date Filter and Sort Controls -->
          <div class="d-flex align-items-center justify-content-between my-2 pb-2 border-bottom border-dark gap-2">
            <div class="d-flex align-items-center gap-1 flex-grow-1 position-relative" style="max-width: 250px;">
              <i class="fa fa-calendar-alt text-muted" style="font-size: 0.75rem;"></i>
              <input type="text" class="form-control form-control-sm bg-dark text-muted border-0 py-0 ps-2 pe-3 todo-date-range-filter"
                placeholder="Filter date / range..." style="font-size: 0.75rem; height: 26px;">
              <button type="button" class="btn btn-sm text-muted p-0 clear-date-filter" title="Clear date filter"
                style="font-size: 0.95rem; line-height: 1; display: none; position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none;">&times;</button>
            </div>
            <div class="d-flex align-items-center gap-1">
              <i class="fa fa-sort text-muted" style="font-size: 0.75rem;"></i>
              <select class="form-select form-select-sm bg-dark text-muted border-0 py-0 ps-1 pe-4 sort-todo-date" style="font-size: 0.75rem; height: 26px;">
                <option value="desc">Newest</option>
                <option value="asc">Oldest</option>
              </select>
            </div>
          </div>

          <div id="todo-container">
            @foreach($tasks as $todo)
              <div class="d-flex align-items-center border-bottom py-2 todo-item" id="todo-{{ $todo->id }}"
                   data-timestamp="{{ $todo->task_time ? $todo->task_time->timestamp : 0 }}">
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
                  <div class="d-flex align-items-center border-bottom py-2 todo-item" id="todo-${response.id}"
                       data-timestamp="${response.timestamp || 0}">
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
            applyTodoFilterAndSort();

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

      // Date Range / Single Date Filter Picker
      let selectedDateRange = [];
      let rangePicker = flatpickr(".todo-date-range-filter", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        altInputClass: "form-control form-control-sm bg-dark text-muted border-0 py-0 ps-2 pe-3",
        theme: "dark",
        onChange: function (selectedDates) {
          selectedDateRange = selectedDates;
          if (selectedDates.length > 0) {
            $('.clear-date-filter').show();
          } else {
            $('.clear-date-filter').hide();
          }
          applyTodoFilterAndSort();
        }
      });

      $(document).on('click', '.clear-date-filter', function () {
        if (Array.isArray(rangePicker)) {
          rangePicker.forEach(p => p.clear());
        } else if (rangePicker && rangePicker.clear) {
          rangePicker.clear();
        }
        selectedDateRange = [];
        $('.clear-date-filter').hide();
        applyTodoFilterAndSort();
      });

      // Filter and Sort Logic for Tasks
      function applyTodoFilterAndSort() {
        let sortVal = $('.sort-todo-date').val() || 'desc';
        let items = $('#todo-container .todo-item').get();

        // Always remove old empty message first
        $('#no-tasks-filter-msg').remove();

        // 1. Sort items by timestamp
        items.sort(function (a, b) {
          let timeA = parseInt($(a).data('timestamp')) || 0;
          let timeB = parseInt($(b).data('timestamp')) || 0;

          if (sortVal === 'asc') {
            return timeA - timeB; // Oldest date first
          } else {
            return timeB - timeA; // Newest date first
          }
        });

        // Re-append in sorted order
        $.each(items, function (idx, item) {
          $('#todo-container').append(item);
        });

        // 2. Filter items by selected date or range
        let hasFilter = selectedDateRange && selectedDateRange.length > 0;
        let startTimestamp = 0;
        let endTimestamp = 0;

        if (hasFilter) {
          let startDate = new Date(selectedDateRange[0]);
          startDate.setHours(0, 0, 0, 0);
          startTimestamp = Math.floor(startDate.getTime() / 1000);

          let endDate = new Date(selectedDateRange.length >= 2 ? selectedDateRange[1] : selectedDateRange[0]);
          endDate.setHours(23, 59, 59, 999);
          endTimestamp = Math.floor(endDate.getTime() / 1000);
        }

        let visibleCount = 0;
        $('#todo-container .todo-item').each(function () {
          let ts = parseInt($(this).data('timestamp')) || 0;
          let show = true;

          if (hasFilter) {
            show = (ts >= startTimestamp && ts <= endTimestamp);
          }

          if (show) {
            $(this).removeClass('d-none');
            visibleCount++;
          } else {
            $(this).addClass('d-none');
          }
        });

        if (hasFilter && visibleCount === 0) {
          $('#todo-container').append('<div id="no-tasks-filter-msg" class="text-center text-muted small py-3">No tasks found for selected date.</div>');
        }

        updateBulkDeleteState();
      }

      // Sort event listener
      $(document).on('change', '.sort-todo-date', function () {
        applyTodoFilterAndSort();
      });

      // Bulk Delete State Tracker
      function updateBulkDeleteState() {
        let selected = $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox:checked');
        let total = $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox').length;
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
        $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox').prop('checked', isChecked);
        updateBulkDeleteState();
      });

      // Bulk Delete Action
      $(document).on('click', '.bulk-delete-btn', function (e) {
        e.preventDefault();
        let ids = [];
        $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox:checked').each(function () {
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