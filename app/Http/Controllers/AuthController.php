<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        $response = Http::post(
            'https://xxxxxxxxxxxxxxxx/login',
            [
                'username' => $request->username,
                'pin' => $request->pin,
                'telegram_init_data' => $request->telegram_init_data,
            ]
        );

        if ($response->successful()) {
            $data = $response->json();
            Session::put('token', $data['token'] ?? null);
            Session::put('user', $data['user'] ?? null);
            // Session::put('', $data[''] ?? null);
            return redirect()
                ->route('dashboard');
        }

        return back()->withErrors([
            'login' => 'بيانات الدخول غير صحيحة',
        ]);
    }

    public function logout()
    {
        Session::flush();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'تم تسجيل الخروج بنجاح'
            );
    }
}
