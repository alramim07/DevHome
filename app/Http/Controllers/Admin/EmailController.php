<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function index(Request $request, DashboardService $service)
    {
        $user = Auth::user();
        $scope = $request->query('scope', 'my');

        if ($scope === 'all' && $user->isSuperAdmin()) {
            $emails = Email::with('user')->latest()->get();
        } else {
            $emails = Email::where('user_id', $user->id)->with('user')->latest()->get();
            $scope = 'my';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'scope' => $scope,
                'count' => $emails->count(),
                'html' => view('admin.partials.email_table_rows', [
                    'emails' => $emails,
                    'isAll' => $scope === 'all',
                ])->render(),
            ]);
        }

        $stats = $service->getStats($user);
        $stats['emails'] = $emails;
        $stats['currentScope'] = $scope;

        return view('admin.email_list', $stats);
    }
}
