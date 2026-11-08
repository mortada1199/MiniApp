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
