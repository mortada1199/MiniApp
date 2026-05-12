@extends('layouts.app')
@section('title', 'تسجيل الدخول')

@section('content')
    <div class="login-wrapper">
        <div class="login-brand">
            <div class="logo">
                <i class="fas fa-university"></i>
            </div>

            <h1>ONB</h1>

            <p>مرحباً بك في بنك ام درمان</p>
        </div>

        <div class="login-form-container">

            <h2>تسجيل الدخول</h2>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">

                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">

                @csrf

                <input type="hidden" name="telegram_init_data" id="telegram_init_data">

                <div class="form-group">

                    <label for="username">
                        اسم المستخدم
                    </label>

                    <input type="text" id="username" name="username" class="form-control"
                        placeholder="أدخل اسم المستخدم" value="{{ old('username') }}" required>

                </div>

                <div class="form-group">

                    <label for="pin">
                        الرقم السري
                    </label>

                    <input type="password" id="pin" name="pin" class="form-control" placeholder="أدخل الرقم السري"
                        required>

                </div>

                <button type="submit" class="btn btn-primary">

                    <i class="fas fa-sign-in-alt"></i>

                    دخول

                </button>

            </form>

        </div>
    </div>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            if (window.Telegram && Telegram.WebApp) {

                Telegram.WebApp.ready();

                document.getElementById('telegram_init_data').value =
                    Telegram.WebApp.initData;

            }

        });
    </script>

@endsection
