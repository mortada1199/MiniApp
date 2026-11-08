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

            <!-- أضفنا id للنموذج لتسهيل التحكم به في الجافاسكريبت -->
            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">

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

                <!-- تم تعديل الزر وإضافة أيقونة التحميل المسبقة من FontAwesome -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="btnText">
                        <i class="fas fa-sign-in-alt"></i> دخول
                    </span>
                    <span id="btnSpinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
                    </span>
                </button>

            </form>

        </div>
    </div>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // كود التليجرام الخاص بك
            if (window.Telegram && Telegram.WebApp) {
                Telegram.WebApp.ready();
                document.getElementById('telegram_init_data').value = Telegram.WebApp.initData;
            }

            // كود تأثير التحميل عند إرسال النموذج
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            if (loginForm) {
                loginForm.addEventListener('submit', function() {
                    // 1. تعطيل الزر لمنع النقرات المتعددة
                    submitBtn.disabled = true;
                    
                    // 2. إخفاء النص الافتراضي (دخول)
                    btnText.style.display = 'none';
                    
                    // 3. إظهار أيقونة التحميل ونص (جاري التحميل...)
                    btnSpinner.style.display = 'inline-block';
                });
            }
        });
    </script>
@endsection