<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;


class AdminController extends Controller
{
    public function index(DashboardService $service)    
    {
        $stats = $service->getStats(Auth::user());
        return view('admin.dashboard', $stats);
    }

    public function loginPageView(Request $request)
    {
        if ($request->session()->get('admin')) {
            return redirect()->route('dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->isSuspended()) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if (Auth::user()->isSuspended()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact the administrator.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function contacts(DashboardService $service)
    {
        $stats = $service->getStats(Auth::user());
        return view('admin.contacts', $stats);
    }


    public function store(Request $request) {
    Todo::create([
        'task' => $request->task,
        'task_time' => $request->task_time,
        'user_id' => Auth::id(),
    ]);
    
    return back();
}
}
