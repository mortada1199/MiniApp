<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
=======
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
>>>>>>> master
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
<<<<<<< HEAD
    public function showLogin()
    {
        return view('auth.login');
=======
    /** اسم الكوكي الذي يحفظ رقم الحساب بعد أول دخول ناجح (مشفر تلقائياً من Laravel) */
    private const REMEMBER_COOKIE = 'remembered_account';

    public function showLogin(Request $request)
    {
        $rememberedAccount = $request->cookie(self::REMEMBER_COOKIE);

        return view('auth.login', compact('rememberedAccount'));
>>>>>>> master
    }

    public function login(Request $request)
    {

        $request->validate([

            'username' => 'required',
            'pin' => 'required',
            'telegram_init_data' => 'required',
        ]);

<<<<<<< HEAD
        $response = Http::post(
            'https://hbcod-196-1-227-87.run.pinggy-free.link/tma/authenticate',
            [
                'username' => $request->username,
                'pin' => $request->pin,
                'telegram_init_data' => $request->telegram_init_data,
            ]
        );
        if ($response->successful()) {
=======
        try {
            $response = $this->tmaApi()->post('/authenticate', [
                'username' => $request->username,
                'pin' => $request->pin,
                'telegram_init_data' => $request->telegram_init_data,
            ]);
        } catch (ConnectionException $e) {
            Log::error('TMA authenticate: connection failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'تعذر الاتصال بخادم البنك، يرجى المحاولة لاحقاً']);
        }

        if ($response->successful()) {
            $request->session()->regenerate();

            // حفظ رقم الحساب لمدة سنة ليظهر تلقائياً في صفحة الدخول
            Cookie::queue(Cookie::make(self::REMEMBER_COOKIE, $request->username, 60 * 24 * 365));
>>>>>>> master
            $data = $response->json();
             Session::put('token', $data['token'] ?? null);
            Session::put('accounts', $data['accounts'] ?? []);
            return redirect()->route('dashboard');
        }
<<<<<<< HEAD
        return back()->withErrors([
            'login' => 'بيانات الدخول غير صحيحة',
        ]);
    }

    public function logout()
    {
        Session::flush();
=======
        if ($response->serverError()) {
            Log::error('TMA authenticate: server error', ['status' => $response->status()]);

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'خادم البنك غير متاح حالياً، يرجى المحاولة لاحقاً']);
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['login' => 'بيانات الدخول غير صحيحة']);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
>>>>>>> master

        return redirect()
            ->route('login')
            ->with(
                'success',
                'تم تسجيل الخروج بنجاح'
            );
    }
}
