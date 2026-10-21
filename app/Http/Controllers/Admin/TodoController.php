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
public function store(Request $request) {
    $request->validate([
        'task' => 'required',
        'task_time' => 'required'
    ]);
    
    $todo = Todo::create([
        'task' => $request->task,
        'task_time' => $request->task_time,
        'user_id' => Auth::id(),
    ]);

    // Return JSON so JavaScript can use the data
    return response()->json([
        'id' => $todo->id,
        'task' => $todo->task,
        'date_formatted' => \Carbon\Carbon::parse($todo->task_time)->format('d M, Y'),
        'timestamp' => \Carbon\Carbon::parse($todo->task_time)->timestamp,
    ]);
}

public function toggle($id) {
    $todo = Todo::findOrFail($id);
    $todo->update(['is_completed' => !$todo->is_completed]);
    return response()->json(['success' => true]);
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
