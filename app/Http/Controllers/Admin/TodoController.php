<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;


class TodoController extends Controller
{
      public function index(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());
        return view('admin.todo', $stats);
    }
    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
            'task_time' => 'required',
        ]);

        $todo = Todo::create([
            'task' => $request->task,
            'task_time' => $request->task_time,
            'is_completed' => false,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'id' => $todo->id,
            'task' => $todo->task,
            'is_completed' => false,
            'status' => 'pending',
            'status_label' => 'Pending',
            'date_formatted' => \Carbon\Carbon::parse($todo->task_time)->format('d M, Y'),
            'date_iso' => \Carbon\Carbon::parse($todo->task_time)->format('Y-m-d'),
            'timestamp' => \Carbon\Carbon::parse($todo->task_time)->timestamp,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        if (! Auth::user()->isSuperAdmin() && $todo->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $status = $request->input('status');
        $isCompleted = in_array(strtolower((string) $status), ['completed', 'complete', '1', 'true'], true);

        $todo->is_completed = $isCompleted;
        $todo->save();

        return response()->json([
            'success' => true,
            'is_completed' => (bool) $todo->is_completed,
            'status' => $todo->is_completed ? 'completed' : 'pending',
            'status_label' => $todo->is_completed ? 'Completed' : 'Pending',
            'message' => 'Task marked as ' . ($todo->is_completed ? 'Completed' : 'Pending') . '.',
        ]);
    }

    public function toggle($id)
    {
        $todo = Todo::findOrFail($id);
        if (! Auth::user()->isSuperAdmin() && $todo->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $todo->is_completed = ! $todo->is_completed;
        $todo->save();

        return response()->json([
            'success' => true,
            'is_completed' => (bool) $todo->is_completed,
            'status' => $todo->is_completed ? 'completed' : 'pending',
            'status_label' => $todo->is_completed ? 'Completed' : 'Pending',
            'message' => 'Task marked as ' . ($todo->is_completed ? 'Completed' : 'Pending') . '.',
        ]);
    }

public function destroy($id)
{
    try {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ], 200);
    } catch (\Exception $e) {
        // This will help you see the error in the Browser Network Tab
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function bulkDestroy(Request $request)
{
    $request->validate([
        'ids' => ['required', 'array'],
        'ids.*' => ['integer'],
    ]);

    try {
        $query = Todo::whereIn('id', $request->ids);
        if (! Auth::user()->isSuperAdmin()) {
            $query->where('user_id', Auth::id());
        }
        $count = $query->delete();

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} task" . ($count === 1 ? '' : 's') . " deleted successfully"
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
