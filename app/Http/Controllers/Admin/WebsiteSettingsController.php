<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteSettingsRequest;
use App\Models\Website_settings;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class WebsiteSettingsController extends Controller
{
    public function index(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());
        $stats['settings'] = $stats['siteSettings'];
        if ($stats['user']->id !== 1) {
            return response()->view('admin.403_redirect', [
                'message' => 'Unauthorized action. Redirecting to dashboard...',
                'url' => route('dashboard'),
            ], 403);
        }

        return view('admin.website_settings', $stats);
    }

    public function update(WebsiteSettingsRequest $request)
    {
        // 1. Get validated data (this only includes fields defined in your Rules)
        $data = $request->validated();

        // 2. Find the first settings record or create a new instance
        $settings = Website_settings::first() ?? new Website_settings();

        // 3. Handle Image Uploads (about_us_img1, about_us_img2, etc.)
        $imageFields = ['about_us_img1', 'about_us_img2', 'logo', 'logo_dark', 'fav_icon'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                if ($settings->$field) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete(media_path($settings->$field));
                }

                $file = $request->file($field);
                $path = $file->store('web-settings', 'public');
                $data[$field] = $path;
            }
        }

        // 4. Update the database
        // We use id 1 to keep it as a single configuration row
        Website_settings::updateOrCreate(['id' => 1], $data);

        \Illuminate\Support\Facades\Cache::forget('site.settings');

        return redirect()->back()->with('success', 'Website settings updated successfully.');
    }
}
