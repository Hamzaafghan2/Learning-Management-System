<?php


namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Attribute\Cache;
use Illuminate\Support\Facades\Cache;


class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Role required to access this route
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {

        if (Auth::check()) {
    $expireTime = Carbon::now()->addSeconds(30);

    Cache::put('user-is-online-' . Auth::id(), true, $expireTime);

    User::where('id', Auth::id())->update([
        'last_seen' => Carbon::now(),
    ]);
}


        // 1️⃣ Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // 2️⃣ If role does not match, redirect to their dashboard
        if ($userRole !== $role) {
            switch ($userRole) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'instructor':
                    return redirect('/instructor/dashboard');
                case 'user':
                    return redirect('/dashboard');
                default:
                    abort(403, 'Unauthorized');
            }
        }

        // 3️⃣ Role matches — continue
        return $next($request);
    }
}



// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Symfony\Component\HttpFoundation\Response;
// class Role
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  \Closure  $next
//      * @param  string  $role
//      * @return mixed
//      */
//     public function handle(Request $request, Closure $next, string $role)
//     {

//           $userRole = $request->user()->role;

//     if ($userRole == 'user' && $role != 'user') {
//         return redirect('dashboard');
//     } elseif ($userRole == 'admin' && $role == 'user') {
//         return redirect('/admin/dashboard');
//     } elseif ($userRole == 'instructor' && $role == 'user') {
//         return redirect('/admin/dashboard');
//     } elseif ($userRole == 'instructor' && $role == 'admin') {
//         return redirect('/admin/dashboard');
//     } elseif ($userRole == 'user' && $role == 'admin') {
//         return redirect('/instructor/dashboard');
//     }


//         // // که نه login شوی وي
//         // if (! Auth::check()) {
//         //     return redirect()->route('login');
//         // }

//         // // که role سم نه وي -> 403
//         // if (Auth::user()->role !== $role) {
//         //     abort(403, 'Unauthorized.');
//         // }

//         return $next($request);
//     }
// }

