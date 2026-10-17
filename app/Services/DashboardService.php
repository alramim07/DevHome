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
        $projects = Projects::where('user_id', $user->id)->latest()->take(5)->get();
        
        return [
            'user'          => $user,
            'contacts'      => Contact::all(),
            'projects'      => $projects,
            'totalProjects' => $projects->count(),
            'totalViews'    => $projects->sum('views'),
            'emails'   => Email::where('user_id', $user->id)->get(),
            'devs' => User::all(),
            'dashemails' => Email::where('user_id', $user->id)->latest()->take(6)->get(),
            'todaysViews'   => ProjectView::whereHas('project', fn($q) => $q->where('user_id', $user->id))
                                ->whereDate('created_at', Carbon::today())->count(),
            'todaysEmails'  => Email::where('user_id', $user->id)
                                ->whereDate('created_at', Carbon::today())->count(),
            'tasks'         => Todo::where('user_id', $user->id)->latest()->take(5)->get(),
            'tasksAll'      => Todo::where('user_id', $user->id)->get(),
            'siteSettings' => Website_settings::first() ?? new Website_settings(),
            'requests' => WantToJoin::all(),
            'listProjects' => Projects::where('user_id', $user->id)->latest()->get(),
            
        ];
    }
}