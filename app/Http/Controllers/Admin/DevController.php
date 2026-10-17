<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddDevRequest;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DevController extends Controller
{
    public function index(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());

        return view('admin.dev_list', $stats);
    }

    public function remove($id)
    {
        $currentUser = Auth::user();
        $targetDev = User::findOrFail($id);
        $dev = User::find($id);

        if ($targetDev->id == 1) {
            return redirect()->route('dev.list')->with('error', 'The Super Admin cannot be removed.');
        }


        if ($currentUser->id !== 1) {
            return redirect()->route('dev.list')->with('error', 'Unauthorized! Only the Super Admin can delete developers.');
        }


        if ($currentUser->id == $targetDev->id) {
            return redirect()->route('dev.list')->with('error', 'You cannot remove yourself.');
        }

        $targetDev->delete();
        Cache::forget('site.team');
        return redirect()->route('dev.list')->with('success', 'Developer removed successfully.');
    }

    public function removeAjax($id)
    {
        $currentUser = Auth::user();
        $targetDev = User::find($id);

        if (!$targetDev) {
            return response()->json(['success' => false, 'message' => 'Developer not found.']);
        }

        if ($targetDev->id == 1) {
            return response()->json(['success' => false, 'message' => 'The Super Admin cannot be removed.']);
        }

        if ($currentUser->id !== 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized! Only the Super Admin can delete developers.']);
        }

        if ($currentUser->id == $targetDev->id) {
            return response()->json(['success' => false, 'message' => 'You cannot remove yourself.']);
        }


        $targetDev->delete();
        Cache::forget('site.team');

        return response()->json([
            'success' => true,
            'message' => 'Developer removed successfully.'
        ]);
    }

    public function addDevView(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());

        if ($stats['user']->id !== 1) {            
            return response()->view('admin.403_redirect', [
                'message' => 'Unauthorized action. Redirecting to dashboard...',
                'url' => route('dashboard'), 
            ], 403);
        
        } else {
            return view('admin.add-dev', $stats);
        }
    }
    public function addDev(AddDevRequest $request)
    {

        $imageName = null;
        

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('dev_images', 'public');
            $imageName = 'storage/' . $path;
        }

        $dev = new User();
        $dev->name = $request->name;
        $dev->email = $request->email;
        $dev->image = $imageName;
        $dev->designation = $request->designation;
        if (empty($request->slug)) {
            $dev->slug = Str::slug($request->title);
        } else {

            $dev->slug = Str::slug($request->slug);
        }
        $dev->facebook_url = $request->facebook_url;
        $dev->linkedin_url = $request->linkedin_url;
        $dev->github_url = $request->github_url;
        $dev->experience = $request->experience;
        $dev->languages = implode(',', $request->input('languages', []));
        $dev->password = bcrypt($request->password);
        $dev->save();
        Cache::forget('site.team');

        return redirect()->route('dev.list')->with('success', 'Developer added successfully.');
    }
}
