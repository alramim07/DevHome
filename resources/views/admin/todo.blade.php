@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <h5 class="mb-0">To Do List</h5>
      <div class="d-flex align-items-center gap-2">
        <div class="form-check m-0 d-flex align-items-center" style="font-size: 0.85rem;">
          <input class="form-check-input select-all-todos" type="checkbox" id="selectAllTodosPage" title="Select All">
          <label class="form-check-label ms-1 text-muted" for="selectAllTodosPage" style="cursor: pointer; user-select: none;">Select All</label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger bulk-delete-btn" style="display: none; padding: 2px 8px; font-size: 0.75rem;">
          <i class="fa fa-trash me-1"></i>Delete (<span class="selected-count">0</span>)
        </button>
      </div>
    </div>

    {{-- Add task form --}}
    <form id="add-todo-form" class="mb-4 pb-3 border-bottom border-dark">
      @csrf
      <div class="row g-2 align-items-center">
        <div class="col-md-5">
          <input name="task" id="task_input" class="form-control bg-dark border-0" type="text" placeholder="Enter task..." required>
        </div>
        <div class="col-md-3">
          <input name="task_time" class="form-control bg-dark border-0 flatpickr-date" type="text" placeholder="Select date" required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Add Task</button>
        </div>
      </div>
    </form>

    {{-- Sort & Filter Controls (Date-wise & Status-wise) --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 my-3 pb-3 border-bottom border-dark">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- 1. Date-wise Calendar Filter / Picker --}}
        <div class="d-flex align-items-center gap-2">
          <label class="small text-muted mb-0 d-flex align-items-center" for="todo-date-filter" style="cursor: pointer;">
            <i class="fa fa-calendar-alt me-1 text-primary"></i>Sort Date:
          </label>
          <div class="position-relative" style="min-width: 210px;">
            <input type="text" id="todo-date-filter"
                   class="form-control form-control-sm bg-dark text-white border-dark pe-4 todo-date-range-filter"
                   placeholder="Select date / range..."
                   style="font-size: 0.8rem; cursor: pointer;">
            <button type="button" class="btn btn-sm text-muted p-0 clear-date-filter" title="Clear date filter"
                    style="display: none; position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.1rem; line-height: 1; z-index: 5;">&times;</button>
          </div>
        </div>

        {{-- 2. Status-wise Sorting / Filter --}}
        <div class="d-flex align-items-center gap-2">
          <label class="small text-muted mb-0 d-flex align-items-center" for="filter-todo-status">
            <i class="fa fa-filter me-1 text-primary"></i>Status:
          </label>
          <select id="filter-todo-status" class="form-select form-select-sm bg-dark text-white border-dark filter-todo-status" style="width: 140px; font-size: 0.8rem; cursor: pointer;">
            <option value="all" selected>All Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
        </div>

        {{-- 3. Order (Newest First / Oldest First) --}}
        <div class="d-flex align-items-center gap-2">
          <label class="small text-muted mb-0 d-flex align-items-center" for="sort-todo-order">
            <i class="fa fa-sort me-1 text-primary"></i>Order:
          </label>
          <select id="sort-todo-order" class="form-select form-select-sm bg-dark text-white border-dark sort-todo-order" style="width: 130px; font-size: 0.8rem; cursor: pointer;">
            <option value="desc" selected>Newest First</option>
            <option value="asc">Oldest First</option>
          </select>
        </div>
      </div>

      <div class="small text-muted">
        Showing <span id="visible-tasks-count" class="text-white">{{ $tasksAll->count() }}</span> of <span id="total-tasks-count" class="text-white">{{ $tasksAll->count() }}</span> task(s)
      </div>
    </div>

    {{-- Tasks List --}}
    <div id="todo-container">
      @forelse($tasksAll as $todo)
        <div class="d-flex align-items-center border-bottom py-3 todo-item" id="todo-{{ $todo->id }}"
             data-id="{{ $todo->id }}"
             data-timestamp="{{ $todo->task_time ? $todo->task_time->timestamp : 0 }}"
             data-date="{{ $todo->task_time ? $todo->task_time->format('Y-m-d') : '' }}"
             data-status="{{ $todo->is_completed ? 'completed' : 'pending' }}">
          <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="{{ $todo->id }}" title="Select task">

          <div class="w-100 ms-3">
            <div class="d-flex w-100 align-items-center justify-content-between flex-wrap gap-2">
              <span class="task-title-text {{ $todo->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                <span class="fw-semibold">{{ $todo->task }}</span>
                <br>
                <small class="text-muted" style="font-size: 0.75rem;">
                  <i class="fa fa-clock me-1"></i>{{ $todo->task_time ? $todo->task_time->format('d M, Y - h:i A') : '' }}
                </small>
              </span>

              <div class="d-flex align-items-center gap-2">
                {{-- Status Dropdown Menu (Completed / Pending) --}}
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-{{ $todo->is_completed ? 'success' : 'danger' }} dropdown-toggle todo-status-dropdown-btn py-1 px-2"
                          type="button" data-bs-toggle="dropdown" aria-expanded="false" id="todo-status-dropdown-{{ $todo->id }}"
                          style="font-size: 0.75rem; min-width: 105px;">
                    <i class="fa fa-circle {{ $todo->is_completed ? 'text-success' : 'text-danger' }} me-1 todo-status-dot" style="font-size: 7px;"></i>
                    <span class="todo-status-text">{{ $todo->is_completed ? 'Completed' : 'Pending' }}</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="todo-status-dropdown-{{ $todo->id }}">
                    <li>
                      <a class="dropdown-item change-todo-status {{ !$todo->is_completed ? 'active' : '' }}" href="javascript:void(0)"
                         data-id="{{ $todo->id }}" data-status="pending">
                        <i class="fa fa-circle text-danger me-2" style="font-size: 7px;"></i>Pending
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item change-todo-status {{ $todo->is_completed ? 'active' : '' }}" href="javascript:void(0)"
                         data-id="{{ $todo->id }}" data-status="completed">
                        <i class="fa fa-circle text-success me-2" style="font-size: 7px;"></i>Completed
                      </a>
                    </li>
                  </ul>
                </div>

                <button class="btn btn-sm btn-outline-danger delete-todo" data-id="{{ $todo->id }}" title="Delete task">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div id="no-tasks-msg" class="text-center text-muted py-5">No tasks found.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
  const csrfToken = '{{ csrf_token() }}';

  // Initialize Flatpickr for task creation date
  flatpickr(".flatpickr-date", {
    dateFormat: "Y-m-d",
    minDate: "today",
    theme: "dark",
    defaultDate: "today",
  });

  // Initialize Flatpickr for Date Filter / Sort Date (range mode)
  let selectedDateRange = [];
  const dateFilterPicker = flatpickr("#todo-date-filter", {
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d M, Y",
    altInputClass: "form-control form-control-sm bg-dark text-white border-dark pe-4 todo-date-range-filter",
    theme: "dark",
    onChange: function (selectedDates) {
      selectedDateRange = selectedDates;
      if (selectedDates && selectedDates.length > 0) {
        $('.clear-date-filter').show();
      } else {
        $('.clear-date-filter').hide();
      }
      applyTodoFilterAndSort();
    }
  });

  // Clear date filter
  $(document).on('click', '.clear-date-filter', function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (dateFilterPicker) {
      if (Array.isArray(dateFilterPicker)) {
        dateFilterPicker.forEach(p => p.clear());
      } else if (typeof dateFilterPicker.clear === 'function') {
        dateFilterPicker.clear();
      }
    }
    selectedDateRange = [];
    $('.clear-date-filter').hide();
    applyTodoFilterAndSort();
  });

  // Apply Filter and Sort Logic
  function applyTodoFilterAndSort() {
    const sortOrder = $('#sort-todo-order').val() || 'desc';
    const filterStatus = $('#filter-todo-status').val() || 'all';
    const items = $('#todo-container .todo-item').get();

    // Remove any previous filter empty message
    $('#no-tasks-filter-msg').remove();

    // 1. Sort items by Date (Newest vs Oldest)
    items.sort(function (a, b) {
      const timeA = parseInt($(a).attr('data-timestamp')) || 0;
      const timeB = parseInt($(b).attr('data-timestamp')) || 0;

      if (sortOrder === 'asc') {
        return timeA - timeB; // Oldest first
      } else {
        return timeB - timeA; // Newest first
      }
    });

    // Re-append sorted items
    $.each(items, function (idx, item) {
      $('#todo-container').append(item);
    });

    // 2. Date Filtering Setup
    const hasDateFilter = selectedDateRange && selectedDateRange.length > 0;
    let startDateStr = '';
    let endDateStr = '';

    if (hasDateFilter) {
      startDateStr = flatpickr.formatDate(selectedDateRange[0], "Y-m-d");
      endDateStr = flatpickr.formatDate(selectedDateRange[selectedDateRange.length >= 2 ? 1 : 0], "Y-m-d");
    }

    // 3. Filter / Show items by Status & Date
    let visibleCount = 0;
    $('#todo-container .todo-item').each(function () {
      const itemStatus = $(this).attr('data-status') || 'pending';
      const itemDate = $(this).attr('data-date') || '';
      let show = true;

      // Status check
      if (filterStatus === 'pending' && itemStatus !== 'pending') {
        show = false;
      } else if (filterStatus === 'completed' && itemStatus !== 'completed') {
        show = false;
      }

      // Date check
      if (show && hasDateFilter) {
        if (!itemDate || itemDate < startDateStr || itemDate > endDateStr) {
          show = false;
        }
      }

      if (show) {
        $(this).removeClass('d-none');
        visibleCount++;
      } else {
        $(this).addClass('d-none');
      }
    });

    $('#visible-tasks-count').text(visibleCount);
    $('#total-tasks-count').text(items.length);

    if (visibleCount === 0 && items.length > 0) {
      $('#todo-container').append('<div id="no-tasks-filter-msg" class="text-center text-muted py-4 small">No tasks match the selected date or status.</div>');
    }

    updateBulkDeleteState();
  }

  // Trigger Filter/Sort on change
  $(document).on('change', '#sort-todo-order, #filter-todo-status', function () {
    applyTodoFilterAndSort();
  });

  // Bulk Delete State Manager
  function updateBulkDeleteState() {
    const selected = $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox:checked');
    const total = $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox').length;
    const count = selected.length;

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

  // Select All toggle (only visible tasks)
  $(document).on('change', '.select-all-todos', function () {
    const isChecked = $(this).is(':checked');
    $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox').prop('checked', isChecked);
    updateBulkDeleteState();
  });

  // Add Task AJAX
  $('#add-todo-form').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();

    $.ajax({
      url: "{{ route('todo.store') }}",
      type: "POST",
      data: formData,
      success: function (response) {
        $('#task_input').val('');
        $('#no-tasks-msg').remove();

        const newTask = `
          <div class="d-flex align-items-center border-bottom py-3 todo-item" id="todo-${response.id}"
               data-id="${response.id}"
               data-timestamp="${response.timestamp || 0}"
               data-date="${response.date_iso || ''}"
               data-status="pending">
            <input class="form-check-input m-0 todo-item-checkbox" type="checkbox" value="${response.id}" title="Select task">
            <div class="w-100 ms-3">
              <div class="d-flex w-100 align-items-center justify-content-between flex-wrap gap-2">
                <span class="task-title-text">
                  <span class="fw-semibold">${response.task}</span>
                  <br>
                  <small class="text-muted" style="font-size: 0.75rem;">
                    <i class="fa fa-clock me-1"></i>${response.date_formatted}
                  </small>
                </span>
                <div class="d-flex align-items-center gap-2">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-danger dropdown-toggle todo-status-dropdown-btn py-1 px-2"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false" id="todo-status-dropdown-${response.id}"
                            style="font-size: 0.75rem; min-width: 105px;">
                      <i class="fa fa-circle text-danger me-1 todo-status-dot" style="font-size: 7px;"></i>
                      <span class="todo-status-text">Pending</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="todo-status-dropdown-${response.id}">
                      <li>
                        <a class="dropdown-item change-todo-status active" href="javascript:void(0)"
                           data-id="${response.id}" data-status="pending">
                          <i class="fa fa-circle text-danger me-2" style="font-size: 7px;"></i>Pending
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item change-todo-status" href="javascript:void(0)"
                           data-id="${response.id}" data-status="completed">
                          <i class="fa fa-circle text-success me-2" style="font-size: 7px;"></i>Completed
                        </a>
                      </li>
                    </ul>
                  </div>
                  <button class="btn btn-sm btn-outline-danger delete-todo" data-id="${response.id}" title="Delete task">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>`;

        $('#todo-container').prepend(newTask);
        applyTodoFilterAndSort();

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

  // Change Status Dropdown Action
  $(document).on('click', '.change-todo-status', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const status = $(this).data('status');
    const statusUrl = "{{ route('todo.status', ':id') }}".replace(':id', id);
    const dropdownBtn = $('#todo-status-dropdown-' + id);
    const menu = dropdownBtn.siblings('.dropdown-menu');
    const todoRow = dropdownBtn.closest('.todo-item');
    const taskTitle = todoRow.find('.task-title-text');

    $.ajax({
      url: statusUrl,
      type: 'POST',
      data: {
        _token: csrfToken,
        status: status
      },
      success: function (response) {
        const isCompleted = response.is_completed;
        const label = response.status_label || (isCompleted ? 'Completed' : 'Pending');

        // Update row data-status
        todoRow.attr('data-status', isCompleted ? 'completed' : 'pending');

        // Update button appearance
        dropdownBtn.removeClass('btn-outline-success btn-outline-danger')
                   .addClass(isCompleted ? 'btn-outline-success' : 'btn-outline-danger');
        dropdownBtn.find('.todo-status-dot')
                   .removeClass('text-success text-danger')
                   .addClass(isCompleted ? 'text-success' : 'text-danger');
        dropdownBtn.find('.todo-status-text').text(label);

        // Update dropdown active item
        menu.find('.change-todo-status').removeClass('active');
        menu.find(`[data-status="${response.status}"]`).addClass('active');

        // Toggle text-decoration on title
        if (isCompleted) {
          taskTitle.addClass('text-decoration-line-through text-muted');
        } else {
          taskTitle.removeClass('text-decoration-line-through text-muted');
        }

        // Re-apply filter if a specific status filter is active
        applyTodoFilterAndSort();

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `Task marked as ${label}`,
          showConfirmButton: false,
          timer: 1500
        });
      },
      error: function () {
        Swal.fire('Error!', 'Could not update task status.', 'error');
      }
    });
  });

  // Bulk Delete Action
  $(document).on('click', '.bulk-delete-btn', function (e) {
    e.preventDefault();
    const ids = [];
    $('#todo-container .todo-item:not(.d-none) .todo-item-checkbox:checked').each(function () {
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
                applyTodoFilterAndSort();
                if ($('#todo-container .todo-item').length === 0) {
                  $('#todo-container').html('<div id="no-tasks-msg" class="text-center text-muted py-5">No tasks found.</div>');
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
    const id = $(this).data('id');
    const row = $('#todo-' + id);

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
              applyTodoFilterAndSort();
              if ($('#todo-container .todo-item').length === 0) {
                $('#todo-container').html('<div id="no-tasks-msg" class="text-center text-muted py-5">No tasks found.</div>');
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

  // Initial sort & filter run
  applyTodoFilterAndSort();
});
</script>
@endpush
