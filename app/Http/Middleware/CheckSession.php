<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $dept = session('dept');
        $sect = session('sect');
        $name = session('name');


        // Check if user is trying to access index-all
        if ($request->route()->getName() === 'index-all') {
            // Allow all departments/sections to access index-all
            return $next($request);
        }

        // For regular index, only allow 6121 department with Kadept or PIC section
        if ($dept === '6121' && in_array($sect, ['Kadept', 'PIC'])) {
            return $next($request);
        }

        // Redirect unauthorized users
        if ($request->route()->getName() === 'index') {
            return redirect()->route('index-all')->with('error', 'You do not have access to this dashboard');
        }

        return $next($request);
    }
}
