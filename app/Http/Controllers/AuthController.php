<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /** اسم الكوكي الذي يحفظ رقم الحساب بعد أول دخول ناجح (مشفر تلقائياً من Laravel) */
    private const REMEMBER_COOKIE = 'remembered_account';

    public function showLogin(Request $request)
    {
        $rememberedAccount = $request->cookie(self::REMEMBER_COOKIE);

        return view('auth.login', compact('rememberedAccount'));
    }

    public function login(Request $request)
    {

        $request->validate([

            'username' => 'required',
            'pin' => 'required',
            'telegram_init_data' => 'required',
        ]);

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
            $data = $response->json();
             Session::put('token', $data['token'] ?? null);
            Session::put('accounts', $data['accounts'] ?? []);
            return redirect()->route('dashboard');
        }
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

        return redirect()
            ->route('login')
            ->with(
                'success',
                'تم تسجيل الخروج بنجاح'
            );
    }
}
