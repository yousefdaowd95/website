<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminToken
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
        if($request->hasHeader('token'))
        {
            $token=base64_decode($request->header('token'));
            $data=json_decode($token);
            if($data->rule == 'Admin')
              //  dd($data->rule);
                return $next($request);
            return response()->json(["massege"=>"Not Auth"],401);
        }
        
    return response()->json(['massege'=>"missed token"],401);
    }
}
