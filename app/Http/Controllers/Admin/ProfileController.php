<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function index(DashboardService $service)
    {
         $data = $service->getStats(Auth::user());

        return view('admin.profile', $data);
    }
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();        

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($user->image) {
                Storage::disk('public')->delete(media_path($user->image));
            }
            $filename = $image->hashName();
            $image->storeAs('images', $filename, 'public');
            $user->image = 'storage/images/' . $filename;
        }

        if ($request->hasFile('custom_cv')) {
            $cv = $request->file('custom_cv');
            if ($user->custom_cv) {
                Storage::disk('public')->delete(media_path($user->custom_cv));
            }
            $cvFilename = $cv->hashName();
            $cv->storeAs('dev_cvs', $cvFilename, 'public');
            $user->custom_cv = 'storage/dev_cvs/' . $cvFilename;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->facebook_url = $request->facebook_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->github_url = $request->github_url;
        $user->experience = $request->experience;
        if ($request->filled('languages')) {
            $user->languages = array_map('trim', explode(',', $request->languages));
        }


        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully!',
                'user' => $user
            ]);
        }
        $user->save();
        Cache::forget('site.team');

        return redirect()->back()->with('success', 'Profile updated!');
    }
}
