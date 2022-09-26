<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class verifyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) // Check incomming request has token or not
            return response()->json(['error'=>'Unauthorized request'], config('global.status.unauthorized'));

        $user = Auth::user();

        if($user->isAdmin()) //Check loged in user has admin role or not
            return $next($request);

        //if not admin user then return unaothorized response
        return response()->json(['error'=>'Unauthorized request'], config('global.status.forbidden'));
    }
}
