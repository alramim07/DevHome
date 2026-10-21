<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\Projects;
use App\Models\ProjectView;
use App\Models\Email;
use App\Models\User;
use App\Models\Todo;
use App\Models\WantToJoin;
use App\Models\Website_settings;
use Carbon\Carbon;

class DashboardService
{
    public function getStats($user)
    {
        $recentProjects = Projects::where('user_id', $user->id)->latest()->take(5)->get();
        $userProjectsQuery = Projects::where('user_id', $user->id);
        
        $stats = [
            'user'          => $user,
            'contacts'      => Contact::all(),
            'projects'      => $recentProjects,
            'totalProjects' => $userProjectsQuery->count(),
            'totalViews'    => (int) $userProjectsQuery->sum('views'),
            'emails'        => Email::where('user_id', $user->id)->get(),
            'devs'          => User::all(),
            'dashemails'    => Email::where('user_id', $user->id)->latest()->take(6)->get(),
            'todaysViews'   => ProjectView::whereHas('project', fn($q) => $q->where('user_id', $user->id))
                                ->whereDate('viewed_at', Carbon::today())->count(),
            'todaysEmails'  => Email::where('user_id', $user->id)
                                ->whereDate('created_at', Carbon::today())->count(),
            'tasks'         => Todo::where('user_id', $user->id)->latest()->get(),
            'tasksAll'      => Todo::where('user_id', $user->id)->get(),
            'siteSettings'  => Website_settings::first() ?? new Website_settings(),
            'requests'      => WantToJoin::all(),
            'listProjects'  => Projects::where('user_id', $user->id)->latest()->get(),
        ];

        // Entire website metrics (for Super Admin)
        if ($user->isSuperAdmin()) {
            $stats['globalTotalProjects']      = Projects::count();
            $stats['globalTotalViews']         = (int) Projects::sum('views');
            $stats['globalTodaysViews']        = ProjectView::whereDate('viewed_at', Carbon::today())->count();
            $stats['globalTodaysJoinRequests'] = WantToJoin::whereDate('created_at', Carbon::today())->count();
        }

        return $stats;
    }
}
