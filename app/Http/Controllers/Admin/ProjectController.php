<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Projects;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProjectController extends Controller
{
    public function index(DashboardService $service)
    {
        $data = $service->getStats(Auth::user());

        return view('admin.projects.add', $data);
    }
    public function store(Request $request)
    {
        $project = new Projects();
        $project->user_id = auth()->id();
        $project->title = $request->title;
        if (empty($request->slug)) {
            $project->slug = Str::slug($request->title);
        } else {

            $project->slug = Str::slug($request->slug);
        }
        $project->description = $request->description;
        $project->github_url = $request->github_url;
        $project->live_url = $request->live_url;


        if ($request->tech_stack) {
            $project->tech_stack = array_map('trim', explode(',', $request->tech_stack));
        }


        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');


            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('project_thumbnail', $filename, 'public');
            $project->thumbnail = 'storage/project_thumbnail/' . $filename;
        }

        $project->save();
        
        // Clear sitemap cache
        Cache::forget('sitemap');
        
        return redirect()->route('list.project')->with('success', 'Project Updated Successfully!');
    }

    public function list(Request $request, DashboardService $service)
    {
        $user = Auth::user();
        $scope = $request->query('scope', 'my');

        if ($scope === 'all' && $user->isSuperAdmin()) {
            $projects = Projects::with('user')->latest()->get();
        } else {
            $projects = Projects::where('user_id', $user->id)->latest()->get();
            $scope = 'my';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'scope' => $scope,
                'count' => $projects->count(),
                'html' => view('admin.projects.partials.table_rows', [
                    'projects' => $projects,
                    'isAll' => $scope === 'all',
                ])->render(),
            ]);
        }

        $data = $service->getStats($user);
        $data['projects'] = $projects;
        $data['currentScope'] = $scope;

        return view('admin.projects.list', $data);
    }

    public function edit(DashboardService $service, $slug)
    {
        $data = $service->getStats(Auth::user());
        $data['project'] = Projects::where('slug', $slug)->firstOrFail();

        return view('admin.projects.edit', $data);
    }

    public function update(Request $request, $slug)
    {
        $project = Projects::where('slug', $slug)->first();
        $project->title = $request->title;
        if (empty($request->slug)) {
            $project->slug = Str::slug($request->title);
        } else {

            $project->slug = Str::slug($request->slug);
        }
        $project->description = $request->description;
        $project->github_url = $request->github_url;
        $project->live_url = $request->live_url;


        if ($request->tech_stack) {
            $project->tech_stack = array_map('trim', explode(',', $request->tech_stack));
        }


        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');


            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('project_thumbnail', $filename, 'public');
            $project->thumbnail = 'storage/project_thumbnail/' . $filename;
        }

        $project->save();
        
        // Clear sitemap cache
        Cache::forget('sitemap');
        
        return redirect()->route('list.project')->with('success', 'Project Updated Successfully!');
    }

    public function destroy($slug)
    {
        $project = Projects::where('slug', $slug)->firstOrFail();
        if ($project->thumbnail) {
            Storage::disk('public')->delete(media_path($project->thumbnail));
        }
        $project->delete();
        
        // Clear sitemap cache
        Cache::forget('sitemap');
        
        return redirect()->route('list.project')->with('success', 'Project removed successfully.');
    }
}
