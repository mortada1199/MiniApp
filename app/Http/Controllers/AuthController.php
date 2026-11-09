<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * الدخول بالبصمة: الصفحة تتحقق من البصمة عبر Telegram BiometricManager،
     * وعند النجاح ترسل telegram_init_data فقط، والـ API يتعرف على العميل من حساب تيليجرام.
     */
    public function login(Request $request)
    {
        $request->validate(
            ['telegram_init_data' => 'required|string'],
            ['telegram_init_data.required' => 'يرجى فتح التطبيق من داخل تيليجرام لتسجيل الدخول']
        );

        try {
            $response = $this->tmaApi()->post('/authenticate', [
                'telegram_init_data' => $request->telegram_init_data,
            ]);
        } catch (ConnectionException $e) {
            Log::error('TMA authenticate: connection failed', ['error' => $e->getMessage()]);

            return back()->withErrors(['login' => 'تعذر الاتصال بخادم البنك، يرجى المحاولة لاحقاً']);
        }

        if ($response->successful()) {
            $request->session()->regenerate();

            $data = $response->json();
            Session::put('token', $data['token'] ?? null);
            Session::put('accounts', $data['accounts'] ?? []);

            return redirect()->route('dashboard');
        }

        if ($response->serverError()) {
            Log::error('TMA authenticate: server error', ['status' => $response->status()]);

            return back()->withErrors(['login' => 'خادم البنك غير متاح حالياً، يرجى المحاولة لاحقاً']);
        }

        return back()->withErrors([
            'login' => $response->json('message') ?? 'لم نتمكن من التعرف على حسابك، تأكد أن حسابك مربوط بتيليجرام',
        ]);
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
