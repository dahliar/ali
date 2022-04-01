<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Auth;
use DB;

class AccessAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $exist = DB::table('user_page_mappings as upm')
        ->join('pages as p', 'p.id', '=', 'upm.pageId')
        ->where('p.route', '=', Route::current()->uri)
        ->where('upm.userId', '=', Auth::user()->id)
        ->where('p.minimumAccessLevel', '>=', Auth::user()->accessLevel)
        ->exists();

        if (!$exist){
            //return redirect('unauthorized')->with('message', "");
            return redirect('unauthorized')->with('message', Route::current()->uri);
        }
        return $next($request);
    }
}
