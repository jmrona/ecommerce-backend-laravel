<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserStatus
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
        if(Auth::user()->status === '1'){
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => 500,
                'ok' => false,
                'msg' => 'Access denied'
            ]);
        }else{
            return $next($request);
        }
    }
}
