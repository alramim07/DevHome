<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\User;
use App\Models\WantToJoin;
use App\Services\HomeService;
use Illuminate\Http\Request;
use App\Http\Requests\JoinRequest;
use App\Models\Notification;
class TeamController extends Controller
{
    public function index(Request $request, HomeService $homeService, $slug)
    {
        try {
            $user = User::where('slug', $slug)->firstOrFail();
            $projects = Projects::where('user_id', $user->id)->get();
            
            $homeData = $homeService->getHomeData($request);
           
            $viewData = array_merge($homeData, ['projects' => $projects, 'user' => $user]);
    
            return view('front.team.index', $viewData);
        } catch (\Throwable $e) {
            \Log::critical('TeamController@index error for slug ' . $slug . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Unable to load team profile');
        }
    }
    public function joinreq (JoinRequest $request)
    {
        $join = new WantToJoin();
        
        $join->name = $request->name;
        $join->link = $request->link;
        $join->save();

        Notification::create([
            'user_id' => 1,
            'type'    => 'join_request',
            'title'   => 'New join request from ' . $request->name,
            'body'    => $request->link,
            'url'     => route('join.list'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Request sent successfully']);
        }

        return redirect()->back()->with('success', 'Request sent successfully');
    }
}
