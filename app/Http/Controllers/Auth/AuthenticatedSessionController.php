<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        /*
        $query = DB::table('employees')
        ->select('id as empid')
        ->select('st as empid')
        ->where('userid', Auth::user()->id)
        ->first();
        */
        $query = DB::table('employees')->select('id as empid')->where('userid', Auth::user()->id)->first();
        
        $levelAccess = DB::table('users as u')
        ->select('sp.levelAccess as levelAccess')
        ->join('employees as e', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->where('eosm.isactive', '=', 1)
        ->where('e.isActive', '=', 1)
        ->where('u.id', '=', Auth::user()->id)
        ->first();

        $request->session()->put('employeeId', $query->empid);
        $request->session()->put('levelAccess', $levelAccess->levelAccess);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
