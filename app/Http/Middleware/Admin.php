<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guard('api')->check()){
            if($request->user()->role=='user') {
                return $next($request);
            }
            else if($request->user()->role=='student') {
                return response()->json("Auth Admin Failed",401);
            }
            else if($request->user()->role=='trainer') {
                return response()->json("Auth Admin Failed",401);
            }
        }
        else{
            return response()->json("Auth Admin Failed",401);
        }
    }
}
