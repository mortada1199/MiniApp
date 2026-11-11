<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // عند انتهاء الجلسة/التوكن (419 Page Expired) أعد المستخدم لصفحة تسجيل الدخول.
        // ملاحظة: الإطار يحوّل TokenMismatchException إلى HttpException(419) قبل هذا الـ callback،
        // لذلك نلتقط الحالة 419 مباشرة.
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null; // اترك باقي الأخطاء لمعالجتها الافتراضية
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت الجلسة، يرجى تسجيل الدخول من جديد',
                ], 419);
            }

            return redirect()->route('login')
                ->with('error', 'انتهت الجلسة، يرجى تسجيل الدخول من جديد');
        });
    })->create();
