<?php

namespace App\Http\Middleware;

use App\Models\Lecturer;
use App\Models\Student;
use Closure;
use Illuminate\Http\Request;

class AddUserInfo
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
        if ($user = $request->user()) {
            if ($user->hasRole('lecturer')) {
                $lecturerID = Lecturer::select('id')->where('user_id', $user->id)->first();
                $user->lecturer_id = $lecturerID->id;
            }
            if ($user->hasRole('student')) {
                $studentID = Student::select('id')->where('user_id', $user->id)->first();
                $user->student_id = $studentID->id;
            }
        }
        return $next($request);
    }
}