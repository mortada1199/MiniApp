@extends('layouts.app')
@section('title', 'تسجيل الدخول')

<<<<<<< HEAD
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
=======
@php
    // رقم الحساب: القيمة المدخلة سابقاً (عند خطأ) أو المحفوظة من آخر دخول ناجح
    $accountValue = old('username', $rememberedAccount ?? '');
    $hasLogo = file_exists(public_path('images/logo.png'));
@endphp

@section('content')
    <style>
        .lg-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        /* ===== الهيدر ===== */
        .lg-hero {
            position: relative;
            padding: 18px 20px 70px;
            color: #fff;
            background:
                radial-gradient(circle at 15% 120%, rgba(74, 173, 98, .35) 0, transparent 55%),
                radial-gradient(circle at 90% -20%, rgba(242, 183, 5, .18) 0, transparent 45%),
                linear-gradient(160deg, #1b2620 0%, #1d3326 55%, #24492f 100%);
            overflow: hidden;
        }

        /* نقش خفيف في الخلفية */
        .lg-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px);
            background-size: 18px 18px;
            pointer-events: none;
        }

        .lg-topbar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .lg-topbar h1 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
        }

        .lg-icon-btn {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            color: #fff;
            border-radius: 12px;
            height: 40px;
            min-width: 40px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
            font-size: .85rem;
            cursor: pointer;
            transition: background .2s;
        }

        .lg-icon-btn:active {
            background: rgba(255, 255, 255, .18);
        }

        .lg-icon-btn i {
            font-size: 1.1rem;
        }

        .lg-brand {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 34px;
            animation: lg-rise .6s ease both;
        }

        .lg-brand img {
            max-height: 90px;
            max-width: 80%;
        }

        /* شعار بديل (حلقة متدرجة) لحين إضافة public/images/logo.png */
        .lg-ring {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: conic-gradient(from 200deg, #2D7D46, #4aad62, #f2b705, #e8a400, #2D7D46);
            -webkit-mask: radial-gradient(circle at 54% 50%, transparent 17px, #000 18px);
            mask: radial-gradient(circle at 54% 50%, transparent 17px, #000 18px);
            filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .35));
        }

        .lg-brand-text {
            line-height: 1.15;
        }

        .lg-brand-text strong {
            display: block;
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .lg-brand-text span {
            font-size: .85rem;
            opacity: .8;
        }

        /* ===== البطاقة البيضاء ===== */
        .lg-card {
            position: relative;
            flex: 1;
            margin-top: -36px;
            background: #fff;
            border-radius: 28px 28px 0 0;
            padding: 30px 24px 24px;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, .08);
            animation: lg-rise .5s .1s ease both;
        }

        .lg-welcome h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 4px;
            color: #111;
        }

        .lg-welcome p {
            margin: 0 0 22px;
            color: #8a8f98;
            font-size: .95rem;
        }

        .lg-alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: .9rem;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .lg-alert.error {
            background: #fdecec;
            color: #b42318;
            animation: lg-shake .4s ease;
        }

        .lg-alert.success {
            background: #e8f5ec;
            color: #1e5c31;
        }

        .lg-field {
            margin-bottom: 18px;
        }

        .lg-field-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .lg-field label {
            font-weight: 700;
            font-size: .95rem;
            color: #111;
        }

        .lg-switch {
            background: none;
            border: 0;
            padding: 0;
            color: #2D7D46;
            font-family: inherit;
            font-size: .8rem;
            cursor: pointer;
            text-decoration: underline;
        }

        .lg-input {
            position: relative;
        }

        .lg-input input {
            width: 100%;
            height: 56px;
            border: 1.5px solid #1f1f1f;
            border-radius: 14px;
            padding: 0 16px 0 52px;
            font-family: inherit;
            font-size: 1.05rem;
            color: #111;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            box-sizing: border-box;
        }

        .lg-input input:focus {
            border-color: #2D7D46;
            box-shadow: 0 0 0 4px rgba(45, 125, 70, .15);
        }

        .lg-input input.is-remembered {
            background: #f6faf7;
            letter-spacing: 1px;
        }

        .lg-input .lg-addon {
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border: 0;
            background: none;
            color: #333;
            font-size: 1.15rem;
            cursor: pointer;
            border-radius: 10px;
        }

        .lg-input .lg-addon.static {
            cursor: default;
            color: #2D7D46;
        }

        .lg-forgot {
            display: inline-block;
            margin: -4px 0 22px;
            color: #2D7D46;
            font-size: .9rem;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .lg-actions {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .lg-submit {
            flex: 1;
            height: 58px;
            border: 0;
            border-radius: 30px;
            background: #5f5f5f;
            color: #fff;
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .25s, transform .1s, box-shadow .25s;
        }

        .lg-submit.ready {
            background: linear-gradient(135deg, #3a9956 0%, #1e5c31 100%);
            box-shadow: 0 8px 20px rgba(45, 125, 70, .35);
        }

        .lg-submit:active {
            transform: scale(.98);
        }

        .lg-submit:disabled {
            opacity: .85;
            cursor: not-allowed;
        }

        .lg-links {
            text-align: center;
            margin-top: 28px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .lg-links a {
            color: #2a5a2f;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 5px;
        }

        .lg-links .muted {
            color: #9ca3af;
            font-weight: 400;
            text-decoration: none;
        }

        .lg-version {
            text-align: center;
            margin-top: 18px;
            font-size: .85rem;
            color: #222;
        }

        /* تنبيه صغير أسفل الشاشة */
        .lg-toast {
            position: fixed;
            left: 50%;
            bottom: 24px;
            transform: translate(-50%, 30px);
            background: #1d3326;
            color: #fff;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: .9rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s, transform .25s;
            max-width: 90%;
            text-align: center;
            z-index: 50;
        }

        .lg-toast.show {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        @keyframes lg-rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        @keyframes lg-shake {
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
    </style>

    <div class="lg-page">
        <header class="lg-hero">
            <div class="lg-topbar">
                <button type="button" class="lg-icon-btn" data-toast="ميزة القائمة ستتوفر قريباً" aria-label="القائمة">
                    <i class="fas fa-bars"></i>
                </button>

                <h1>تسجيل الدخول</h1>

                <button type="button" class="lg-icon-btn" data-toast="اللغة الإنجليزية ستتوفر قريباً">
                    اللغة <i class="fas fa-globe"></i>
                </button>
            </div>

            <div class="lg-brand">
                @if ($hasLogo)
                    <img src="{{ asset('images/logo.png') }}" alt="الشعار">
                @else
                    <div class="lg-ring" aria-hidden="true"></div>
                    <div class="lg-brand-text">
                        <strong>ONB</strong>
                        <span>بنك ام درمان الوطني</span>
                    </div>
                @endif
            </div>
        </header>

        <main class="lg-card">
            <div class="lg-welcome">
                <h2 id="greeting">مرحباً</h2>
                <p>قم بتسجيل الدخول إلى حسابك</p>
            </div>

            @if (session('success'))
                <div class="lg-alert success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
>>>>>>> master
                </div>
            @endif

            @if ($errors->any())
<<<<<<< HEAD
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
=======
                <div class="lg-alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm" novalidate>
                @csrf
                <input type="hidden" name="telegram_init_data" id="telegram_init_data">

                <div class="lg-field">
                    <div class="lg-field-head">
                        <label for="username">رقم الحساب</label>
                        @if ($rememberedAccount)
                            <button type="button" class="lg-switch" id="switchAccount">ليس حسابك؟</button>
                        @endif
                    </div>
                    <div class="lg-input">
                        <input type="text" id="username" name="username" inputmode="numeric" autocomplete="username"
                            value="{{ $accountValue }}" placeholder="أدخل رقم الحساب"
                            class="{{ $rememberedAccount && $accountValue === $rememberedAccount ? 'is-remembered' : '' }}"
                            required>
                        <span class="lg-addon static"><i class="far fa-user"></i></span>
                    </div>
                </div>

                <div class="lg-field">
                    <div class="lg-field-head">
                        <label for="pin">كلمة المرور</label>
                    </div>
                    <div class="lg-input">
                        <input type="password" id="pin" name="pin" autocomplete="current-password"
                            placeholder="أدخل كلمة المرور" required>
                        <button type="button" class="lg-addon" id="togglePin" aria-label="إظهار كلمة المرور">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <a href="#" class="lg-forgot" data-toast="لاستعادة كلمة المرور يرجى التواصل مع أقرب فرع">
                    هل نسيت كلمة المرور؟
                </a>

                <div class="lg-actions">
                    <button type="submit" class="lg-submit" id="submitBtn">
                        <span id="btnText">تسجيل الدخول</span>
                        <span id="btnSpinner" style="display:none">
                            <i class="fas fa-spinner fa-spin"></i> جاري التحقق...
                        </span>
                    </button>
                </div>
            </form>

            <nav class="lg-links">
                <a href="#" class="muted" data-toast="للمساعدة تواصل مع خدمة العملاء">مساعدة</a>
            </nav>

            <div class="lg-version">الإصدار {{ config('app.version', '1.0') }}</div>
        </main>
    </div>

    <div class="lg-toast" id="toast" role="status"></div>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const account = document.getElementById('username');
            const pin = document.getElementById('pin');
            const submitBtn = document.getElementById('submitBtn');

            // ===== تليجرام =====
            if (window.Telegram && Telegram.WebApp) {
                const tg = Telegram.WebApp;
                tg.ready();
                tg.expand();
                document.getElementById('telegram_init_data').value = tg.initData;
                try {
                    tg.setHeaderColor('#1b2620');
                } catch (e) {}

                const name = tg.initDataUnsafe && tg.initDataUnsafe.user && tg.initDataUnsafe.user.first_name;
                if (name) document.getElementById('greeting').textContent = 'مرحباً، ' + name;
            }

            // ===== تفعيل الزر عند تعبئة الحقول =====
            function refreshButton() {
                submitBtn.classList.toggle('ready', account.value.trim() !== '' && pin.value.trim() !== '');
            }
            account.addEventListener('input', function() {
                account.classList.remove('is-remembered');
                refreshButton();
            });
            pin.addEventListener('input', refreshButton);
            refreshButton();

            // لو رقم الحساب محفوظ ننقل التركيز مباشرة لكلمة المرور
            (account.value ? pin : account).focus();

            // ===== إظهار/إخفاء كلمة المرور =====
            document.getElementById('togglePin').addEventListener('click', function() {
                const show = pin.type === 'password';
                pin.type = show ? 'text' : 'password';
                this.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                pin.focus();
            });

            // ===== تغيير الحساب المحفوظ =====
            const switchBtn = document.getElementById('switchAccount');
            if (switchBtn) {
                switchBtn.addEventListener('click', function() {
                    account.value = '';
                    account.classList.remove('is-remembered');
                    switchBtn.remove();
                    account.focus();
                    refreshButton();
                });
            }

            // ===== التنبيهات =====
            const toast = document.getElementById('toast');
            let toastTimer;

            function showToast(msg) {
                toast.textContent = msg;
                toast.classList.add('show');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
            }
            document.querySelectorAll('[data-toast]').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    showToast(el.dataset.toast);
                });
            });

            // ===== الإرسال =====
            form.addEventListener('submit', function(e) {
                if (!account.value.trim() || !pin.value.trim()) {
                    e.preventDefault();
                    showToast('يرجى إدخال رقم الحساب وكلمة المرور');
                    (account.value.trim() ? pin : account).focus();
                    return;
                }
                submitBtn.disabled = true;
                document.getElementById('btnText').style.display = 'none';
                document.getElementById('btnSpinner').style.display = 'inline-block';
            });
        });
    </script>
@endsection
>>>>>>> master
