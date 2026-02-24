<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('student')->check()) {
            return redirect()->route('student.login')->with('error', 'Please login to continue.');
        }
        return $next($request);
    }
}
