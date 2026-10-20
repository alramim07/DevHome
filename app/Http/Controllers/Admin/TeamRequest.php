<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WantToJoin;
use App\Services\DashboardService;
use Auth;
use Illuminate\Http\Request;

class TeamRequest extends Controller
{
    public function index(DashboardService $service)
    {
        $user = Auth::user();
        if ($user->id !== 1) {
            return response()->view('admin.403_redirect', [
                'message' => 'Unauthorized action. Redirecting to dashboard...',
                'url' => route('dashboard'),
            ], 403);
        }
        $requests = $service->getStats($user);
        return view('admin.team-request', $requests);
    }

    public function destroy($id)
    {
        $request = WantToJoin::find($id);
        if (!$request) {
            return response()->json(['success' => false, 'message' => 'Request not found']);
        }
        $request->delete();
        return response()->json(['success' => true, 'message' => 'Request deleted successfully']);
    }
}
