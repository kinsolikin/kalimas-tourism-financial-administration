<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(!$request->session()->has('shift')&& !$request->session()->has('employe')){
            return redirect()->route('dashboard.shift')->with('error','Anda belum memiliki shift yang aktif, silahkan pilih shift terlebih dahulu.');
        }
        return $next($request);
    }
}
