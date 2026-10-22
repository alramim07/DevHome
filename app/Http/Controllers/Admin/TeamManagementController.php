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
use Illuminate\Support\Str;

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
        $stats['positions'] = Position::withCount([
            'users' => function ($query) {
                $query->whereNull('users.deleted_at')
                      ->where(function ($q) {
                          $q->where('users.status', 'active')
                            ->orWhereNull('users.status');
                      });
            }
        ])->orderBy('name')->get();
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
        $stats['teams'] = Team::with(['users' => function ($query) {
            $query->whereNull('users.deleted_at');
        }])->withCount([
            'users as active_members_count' => function ($query) {
                $query->whereNull('users.deleted_at')
                      ->where(function ($q) {
                          $q->where('users.status', 'active')
                            ->orWhereNull('users.status');
                      });
            }
        ])->orderBy('name')->get();

        // Candidates for the "add member" dropdown: active non-deleted users.
        $stats['teamCandidates'] = User::with('position')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'image', 'position_id']);

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
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $ids = [];
        if ($request->has('user_ids')) {
            $ids = array_map('intval', (array) $request->input('user_ids', []));
        } elseif ($request->filled('user_id')) {
            $ids = [(int) $request->input('user_id')];
        }

        if (empty($ids)) {
            return redirect()->route('team.teams')->with('error', 'Please select at least one member to add.');
        }

        $existingIds = $team->users()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
        $newIds = array_values(array_diff($ids, $existingIds));

        if (empty($newIds)) {
            $singleUser = count($ids) === 1 ? User::find($ids[0]) : null;
            $msg = $singleUser ? ($singleUser->name . ' is already in ' . $team->name . '.') : ('Selected member(s) are already in ' . $team->name . '.');
            return redirect()->route('team.teams')->with('error', $msg);
        }

        $team->users()->attach($newIds);
        Cache::forget('site.team');

        if (count($newIds) === 1) {
            $firstUser = User::find($newIds[0]);
            $msg = ($firstUser?->name ?? 'Member') . ' added to ' . $team->name . '.';
        } else {
            $msg = count($newIds) . ' members added to ' . $team->name . ' successfully.';
        }

        return redirect()->route('team.teams')->with('success', $msg);
    }

    public function removeMember(Team $team, $userId)
    {
        $user = User::findOrFail($userId);
        $team->users()->detach($user->id);
        Cache::forget('site.team');

        return redirect()->route('team.teams')->with('success', $user->name . ' removed from ' . $team->name . '.');
    }

    // ------------------------------------------------------------------
    // All Members — list, create, edit, delete, restore
    // ------------------------------------------------------------------

    public function members(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());

        $query = User::with(['position', 'teams'])->withTrashed();

        // Filters: position, team, status (active|deleted|all).
        $filters = [
            'position_id' => request()->integer('position_id') ?: null,
            'team_id' => request()->integer('team_id') ?: null,
            'status' => request()->query('status', 'active'),
        ];

        if ($filters['position_id']) {
            $query->where('position_id', $filters['position_id']);
        }
        if ($filters['team_id']) {
            $query->whereHas('teams', fn ($q) => $q->where('teams.id', $filters['team_id']));
        }
        if ($filters['status'] === 'deleted') {
            $query->onlyTrashed();
        } elseif ($filters['status'] === 'suspended' || $filters['status'] === 'suspanded') {
            $query->whereNull('deleted_at')->where('status', 'suspended');
        } elseif ($filters['status'] === 'active_only') {
            $query->whereNull('deleted_at')->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            });
        } elseif ($filters['status'] === 'all') {
            // All records including trashed
        } else {
            $query->whereNull('deleted_at');
        }

        $stats['members'] = $query->orderBy('name')->get();
        $stats['positions'] = Position::orderBy('name')->get();
        $stats['teams'] = Team::orderBy('name')->get();
        $stats['filters'] = $filters;
        $stats['sectionsLabels'] = User::SECTIONS_LABELS;
        $stats['rolesLabels'] = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'member' => 'Member'];

        return view('admin.team.members', $stats);
    }

    public function createMember(DashboardService $service)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $stats = $service->getStats(Auth::user());
        $stats['positions'] = Position::orderBy('name')->get();
        $stats['teams'] = Team::orderBy('name')->get();
        $stats['sectionsLabels'] = User::SECTIONS_LABELS;
        $stats['rolesLabels'] = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'member' => 'member'];

        // Preset pre-fill: ?position_id=x ticks that position's default sections.
        $position = Position::find(request()->integer('position_id'));
        $stats['presetPermissions'] = $position?->permissions ?? [];
        $stats['selectedPositionId'] = $position?->id;

        return view('admin.team.member-form', $stats + ['mode' => 'create', 'member' => null]);
    }

    public function storeMember(Request $request)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $data = $this->validateMember($request, true);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = $data['role'];
        $user->position_id = $data['position_id'] ?? null;
        $user->permissions = $data['permissions'] ?? [];
        $user->status = ($data['status'] ?? 'active') === 'suspanded' ? 'suspended' : ($data['status'] ?? 'active');
        $user->slug = $this->uniqueSlug($data['name']);
        if ($position = Position::find($data['position_id'] ?? null)) {
            $user->designation = $position->name;
        } else {
            $user->designation = $data['designation'] ?? null;
        }

        if ($request->hasFile('image')) {
            $user->image = 'storage/' . $request->file('image')->store('dev_images', 'public');
        }

        $user->save();

        if (! empty($data['team_ids'])) {
            $user->teams()->sync($data['team_ids']);
        }

        Cache::forget('site.team');

        return redirect()->route('team.members')->with('success', $user->name . ' created successfully.');
    }

    public function editMember(DashboardService $service, $userId)
    {
        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $user = User::with('teams')->findOrFail($userId);

        $stats = $service->getStats(Auth::user());
        $stats['positions'] = Position::orderBy('name')->get();
        $stats['teams'] = Team::orderBy('name')->get();
        $stats['sectionsLabels'] = User::SECTIONS_LABELS;
        $stats['rolesLabels'] = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'member' => 'Member'];
        $stats['member'] = $user;
        $stats['selectedPositionId'] = $user->position_id;
        $stats['presetPermissions'] = $user->permissions ?? [];

        return view('admin.team.member-form', $stats + ['mode' => 'edit']);
    }

    public function updateMember(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        // Mutual super-admin protection: another super admin is untouchable.
        if ($user->isSuperAdmin()) {
            return response()->view('admin.403_redirect', [
                'message' => 'A Super Admin cannot change another Super Admin.',
                'url' => route('team.members'),
            ], 403);
        }

        $data = $this->validateMember($request, false, $user);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->position_id = $data['position_id'] ?? null;
        $user->permissions = $data['permissions'] ?? [];
        if (isset($data['status'])) {
            $user->status = ($data['status'] === 'suspanded') ? 'suspended' : $data['status'];
        }
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        if ($position = Position::find($data['position_id'] ?? null)) {
            $user->designation = $position->name;
        }

        if ($request->hasFile('image')) {
            $user->image = 'storage/' . $request->file('image')->store('dev_images', 'public');
        }

        $user->save();
        $user->teams()->sync($data['team_ids'] ?? []);

        Cache::forget('site.team');

        return redirect()->route('team.members')->with('success', $user->name . ' updated successfully.');
    }

    public function deleteMember(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $actor = Auth::user();

        if (! $actor->isSuperAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only the Super Admin can delete members.'], 403);
            }
            return response()->view('admin.403_redirect', [
                'message' => 'Only the Super Admin can delete members.',
                'url' => route('dashboard'),
            ], 403);
        }

        if ($actor->id === $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete yourself.'], 422);
            }
            return redirect()->route('team.members')->with('error', 'You cannot delete yourself.');
        }

        if ($user->isSuperAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'A Super Admin cannot delete another Super Admin.'], 403);
            }
            return response()->view('admin.403_redirect', [
                'message' => 'A Super Admin cannot delete another Super Admin.',
                'url' => route('team.members'),
            ], 403);
        }

        $user->delete();
        Cache::forget('site.team');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $user->name . ' deleted successfully.',
            ]);
        }

        return redirect()->route('team.members')->with('success', $user->name . ' deleted.');
    }

    public function bulkDeleteMembers(Request $request)
    {
        $actor = Auth::user();

        if (! $actor->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only the Super Admin can delete members.'], 403);
        }

        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = collect($request->input('ids', []))
            ->reject(fn ($id) => (int) $id === (int) $actor->id)
            ->all();

        $users = User::whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($users as $user) {
            if (! $user->isSuperAdmin()) {
                $user->delete();
                $deletedCount++;
            }
        }

        Cache::forget('site.team');

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} member(s) deleted successfully.",
            'deleted_count' => $deletedCount,
        ]);
    }

    public function updateStatus(Request $request, $userId)
    {
        $actor = Auth::user();

        if (! $actor->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only the Super Admin can change member status.'], 403);
        }

        $user = User::findOrFail($userId);

        if ($actor->id === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot change your own status.'], 422);
        }

        if ($user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot modify Super Admin status.'], 403);
        }

        $request->validate([
            'status' => ['required', 'string', 'in:active,suspended,suspanded'],
        ]);

        $status = strtolower($request->status) === 'suspanded' ? 'suspended' : strtolower($request->status);
        $user->status = $status;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'status_label' => $user->status === 'suspended' ? 'Suspanded' : 'Active',
            'message' => "Member status updated to " . ($user->status === 'suspended' ? 'Suspanded' : 'Active') . ".",
        ]);
    }

    public function restoreMember($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);

        if ($denied = $this->authorizeSuperAdmin()) {
            return $denied;
        }

        $user->restore();
        Cache::forget('site.team');

        return redirect()->route('team.members')->with('success', $user->name . ' restored.');
    }

    /**
     * Shared validation for create/update. Returns validated data with
     * hashed password (Laravel's hashed cast handles it on assignment).
     */
    protected function validateMember(Request $request, bool $isCreate, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', ($isCreate ? 'unique:users,email' : 'unique:users,email,' . $user->id)],
            'role' => ['required', 'in:' . implode(',', User::ROLES)],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['integer', 'exists:teams,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', User::SECTIONS)],
            'status' => ['nullable', 'string', 'in:active,suspended,suspanded'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'password' => [($isCreate ? 'required' : 'nullable'), 'string', 'min:8'],
        ];

        return $request->validate($rules);
    }

    protected function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name) ?: 'user';
        $base = $slug;
        $n = 1;
        while (User::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }
}
