<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\ProjectContactRequest;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Projects;
use App\Models\User;
use App\Models\Website_settings;
use Illuminate\Http\Request;
use App\Models\ProjectView;
use Illuminate\Support\Carbon;
use App\Services\HomeService;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request, HomeService $homeService)
    {

        try {
            $data = $homeService->getHomeData($request);

            if ($request->ajax()) {
                return view('front.partials.project_cards', [
                    'projects' => $data['projects']
                ])->render();
            }

            return view('front.layouts.home.index', $data);
        } catch (\Exception $e) {
            Log::error('HomeController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: return a minimal homepage without crashing
            $fallbackData = [
                'projects' => collect(),
                'teams' => collect(),
                'settings' => new \App\Models\Website_settings(),
            ];
            
            if ($request->ajax()) {
                return view('front.partials.project_cards', [
                    'projects' => collect()
                ])->render();
            }
            
            return view('front.layouts.home.index', $fallbackData);
        }
    }


    public function project(Request $request, $slug)
    {
        $projects = Projects::where('slug', $slug)->firstOrFail();
        $ip = $request->ip();
        $settings = Website_settings::first();

        $alreadyViewed = ProjectView::where('project_id', $projects->id)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->exists();

        if (!$alreadyViewed) {
            $projects->increment('views');

            ProjectView::create([
                'project_id' => $projects->id,
                'ip_address' => $ip,
            ]);
        }

        $user = User::where('id', $projects->user_id)->firstOrFail();

        return view('front.layouts.home.project', compact('projects', 'user', 'settings'));
    }

    /**
     * Public single blog post (published only). Drafts, unknown slugs and a
     * /blog index all 404 — the blog has no public listing by design.
     */
    public function blogShow($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        $settings = Website_settings::first();

        $suggested = Blog::published()
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $author = User::withTrashed()->find($blog->user_id);

        return view('front.layouts.home.blog', compact('blog', 'settings', 'suggested', 'author'));
    }

    public function submit(ContactRequest $request)
    {

        Contact::create($request->validated());

        Notification::create([
            'user_id' => 1,
            'type'    => 'contact',
            'title'   => 'New contact from ' . $request->first_name . ' ' . $request->last_name,
            'body'    => Str::limit($request->message, 80),
            'url'     => route('website.contacts'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "We've Received Your SMS, Thank You"
        ]);
    }
    public function contactMe(ProjectContactRequest $request)
    {        
        if ($request->has('user_id')) {           
            $targetUserId = $request->user_id;
        } else {
            $project = Projects::findOrFail($request->project_id);
            $targetUserId = $project->user_id;
        }

        Email::create(array_merge($request->validated(), [
            'user_id' => $targetUserId,
            'project_id' => $request->project_id ?? null
        ]));

        Notification::create([
            'user_id' => $targetUserId,
            'type'    => 'email',
            'title'   => 'New email from ' . $request->first_name . ' ' . $request->last_name,
            'body'    => Str::limit($request->message, 80),
            'url'     => route('email.list'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "I've Received Your Message, Thank You"
        ]);
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|file|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // 1. Generate a safe name derived from the file's real (validated) type,
            //    never from the client-supplied extension.
            $fileName = $file->hashName();

            // 2. Store the image in storage/app/public/project-images
            $path = $file->storeAs('project-images', $fileName, 'public');

            // 3. Generate the public URL
            $url = media_url($path);

            return response()->json([
                'fileName' => $fileName,
                'uploaded' => 1,
                'url' => $url
            ]);
        }
    }
}
