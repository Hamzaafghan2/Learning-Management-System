<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('frontend.dashboard.login');
    }

    /**
     * Handle an incoming authentication request.
     */


    public function store(LoginRequest $request)
{
    // authenticate user
    $request->authenticate();

     $notification = array(
        'message'=>"Login Successfully",
        'alert-type'=>'success'
    );

    // default redirect url
    $url = '';

    // check user role
    if ($request->user()->role === 'admin') {
        $url = '/admin/dashboard';
    } elseif ($request->user()->role === 'instructor') {
        $url = '/instructor/dashboard';
    } elseif ($request->user()->role === 'user') {
        $url = '/dashboard';
    }

    // regenerate session for security
    $request->session()->regenerate();

    // redirect to the correct dashboard
    return redirect()->intended($url)->with($notification);
}


// public function store(Request $request): RedirectResponse
// {
//     $credentials = $request->validate([
//         'email' => 'required|email',
//         'password' => 'required',
//     ]);

//     if (Auth::attempt($credentials)) {
//         $request->session()->regenerate();

//         $user = Auth::user();

//         if ($user->role === 'admin') {
//             return redirect()->intended('/admin/dashboard');
//         } elseif ($user->role === 'instructor') {
//             return redirect()->intended('/instructor/dashboard');
//         } else {
//             return redirect()->intended('/dashboard');
//         }
//     }

//     return back()->withErrors([
//         'email' => 'The provided credentials do not match our records.',
//     ]);
// }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
