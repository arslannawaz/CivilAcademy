<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Trainer
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
                return response()->json("Auth Trainer Failed",401);
            }
            else if($request->user()->role=='student') {
                return response()->json("Auth Trainer Failed",401);
            }
            else if($request->user()->role=='trainer') {
                return $next($request);
            }
        }
        else{
            return response()->json("Auth Trainer Failed",401);
        }
    }
}
