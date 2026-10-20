<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TeamManagementController extends Controller
{
    /**
     * Guard for operations reserved to the super admin (position writes,
     * user creation/role/permission changes — the latter in Task 5).
     */
    protected function authorizeSuperAdmin()
    {
        if (! Auth::user()->isSuperAdmin()) {
            return response()->view('admin.403_redirect', [
                'message' => 'Unauthorized action. Redirecting to dashboard...',
                'url' => route('dashboard'),
            ], 403);
        }

        return null;
    }

    /**
     * Validate a permissions array against the canonical sections list.
     */
    protected function validatePermissions(Request $request): void
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', User::SECTIONS)],
        ]);
    }

    // ------------------------------------------------------------------
    // Positions
    // ------------------------------------------------------------------

    public function positions(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());
        $stats['positions'] = Position::withCount('users')->orderBy('name')->get();
        $stats['sectionsLabels'] = User::SECTIONS_LABELS;

        return view('admin.team.positions', $stats);
    }

    public function storePosition(Request $request)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:positions,name'],
        ]);
        $this->validatePermissions($request);

        Position::create([
            'name' => $request->name,
            'permissions' => $request->input('permissions', []),
        ]);

        return redirect()->route('team.positions')->with('success', 'Position created successfully.');
    }

    public function updatePosition(Request $request, Position $position)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:positions,name,' . $position->id],
        ]);
        $this->validatePermissions($request);

        $position->update([
            'name' => $request->name,
            'permissions' => $request->input('permissions', []),
        ]);

        return redirect()->route('team.positions')->with('success', 'Position updated successfully.');
    }

    public function destroyPosition(Position $position)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $position->delete();

        return redirect()->route('team.positions')->with('success', 'Position deleted successfully.');
    }

    // ------------------------------------------------------------------
    // Teams
    // ------------------------------------------------------------------

    public function teams(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());
        $stats['teams'] = Team::with('users')->orderBy('name')->get();
        // Candidates for the "add member" dropdown: every non-deleted user.
        $stats['teamCandidates'] = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.team.teams', $stats);
    }

    public function storeTeam(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:teams,name'],
        ]);

        Team::create(['name' => $request->name]);

        return redirect()->route('team.teams')->with('success', 'Team created successfully.');
    }

    public function updateTeam(Request $request, Team $team)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:teams,name,' . $team->id],
        ]);

        $team->update(['name' => $request->name]);

        return redirect()->route('team.teams')->with('success', 'Team updated successfully.');
    }

    public function destroyTeam(Team $team)
    {
        $team->delete();

        return redirect()->route('team.teams')->with('success', 'Team deleted successfully.');
    }

    public function addMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if ($team->users()->where('users.id', $user->id)->exists()) {
            return redirect()->route('team.teams')->with('error', $user->name . ' is already in ' . $team->name . '.');
        }

        $team->users()->attach($user->id);
        Cache::forget('site.team');

        return redirect()->route('team.teams')->with('success', $user->name . ' added to ' . $team->name . '.');
    }

    public function removeMember(Team $team, $userId)
    {
        $user = User::findOrFail($userId);
        $team->users()->detach($user->id);
        Cache::forget('site.team');

        return redirect()->route('team.teams')->with('success', $user->name . ' removed from ' . $team->name . '.');
    }
}
