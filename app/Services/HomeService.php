<?php

namespace App\Services;

use App\Models\User;
use App\Models\Website_settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class HomeService
 * @package App\Services
 */
class HomeService
{

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function getHomeData(Request $request)
    {
        return [
            'projects' => $this->projectService->getFilteredProjects($request),
            'teams'    => Cache::remember('site.team', 600, fn () => User::has('teams')
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                })
                ->get()),
            'settings' => Cache::remember('site.settings', 600, fn () => Website_settings::first() ?? new Website_settings()),
        ];
    }
}
