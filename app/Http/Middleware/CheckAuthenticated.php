<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * يسمح بالدخول للصفحات المحمية فقط إذا كان العميل مسجّل دخول،
 * أي أن توكن البنك محفوظ في السيشن (يُحفظ في AuthController@login عند نجاح الدخول).
 */
class CheckAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! Session::get('token')) {
            // طلبات AJAX (مثل التحقق من اسم العميل) ترجع 401 بدل تحويلها لصفحة HTML
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت الجلسة، يرجى تسجيل الدخول مرة أخرى',
                ], 401);
            }

            return redirect()->route('login')->withErrors([
                'login' => 'يرجى تسجيل الدخول للمتابعة',
            ]);
        }

        return $next($request);
    }
}
