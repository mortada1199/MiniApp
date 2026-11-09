@extends('layouts.app')
@section('title', 'تسجيل الدخول')

@php
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

        /* ===== البطاقة ===== */
        .lg-card {
            position: relative;
            flex: 1;
            margin-top: -36px;
            background: #fff;
            border-radius: 28px 28px 0 0;
            padding: 30px 24px 24px;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, .08);
            display: flex;
            flex-direction: column;
            animation: lg-rise .5s .1s ease both;
        }

        .lg-welcome h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 4px;
            color: #111;
        }

        .lg-welcome p {
            margin: 0 0 18px;
            color: #8a8f98;
            font-size: .95rem;
        }

        .lg-alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
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

        /* ===== زر البصمة ===== */
        .lg-bio-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 0 8px;
        }

        .lg-bio-wrap {
            position: relative;
            width: 170px;
            height: 170px;
            display: grid;
            place-items: center;
        }

        /* حلقات النبض */
        .lg-bio-wrap::before,
        .lg-bio-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(45, 125, 70, .35);
            opacity: 0;
            /* الحلقات للزينة فقط: لا تمنع الضغط على زر البصمة */
            pointer-events: none;
        }

        .lg-bio-area.ready .lg-bio-wrap::before,
        .lg-bio-area.ready .lg-bio-wrap::after {
            animation: lg-pulse 2.4s ease-out infinite;
        }

        .lg-bio-area.ready .lg-bio-wrap::after {
            animation-delay: 1.2s;
        }

        .lg-bio {
            position: relative;
            z-index: 1;
            width: 128px;
            height: 128px;
            border-radius: 50%;
            border: 0;
            color: #fff;
            font-size: 3.6rem;
            cursor: pointer;
            background: linear-gradient(145deg, #3a9956 0%, #1e5c31 100%);
            box-shadow: 0 14px 30px rgba(30, 92, 49, .35), inset 0 2px 0 rgba(255, 255, 255, .15);
            transition: transform .15s, background .3s, box-shadow .3s;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        .lg-bio:active {
            transform: scale(.95);
        }

        .lg-bio:disabled {
            cursor: default;
        }

        /* خط المسح أثناء التحقق */
        .lg-bio::after {
            content: '';
            position: absolute;
            left: 15%;
            right: 15%;
            height: 3px;
            top: 20%;
            border-radius: 3px;
            background: #f2b705;
            box-shadow: 0 0 12px #f2b705;
            opacity: 0;
        }

        .lg-bio-area.scanning .lg-bio::after {
            opacity: 1;
            animation: lg-scan 1.2s ease-in-out infinite alternate;
        }

        .lg-bio-area.success .lg-bio {
            background: linear-gradient(145deg, #4aad62 0%, #2D7D46 100%);
        }

        .lg-bio-area.error .lg-bio {
            background: linear-gradient(145deg, #d9534f 0%, #a52a26 100%);
            box-shadow: 0 14px 30px rgba(165, 42, 38, .3);
            animation: lg-shake .4s ease;
        }

        .lg-bio-area.disabled .lg-bio {
            background: linear-gradient(145deg, #a3a3a3 0%, #6b6b6b 100%);
            box-shadow: none;
        }

        .lg-status {
            margin-top: 22px;
            text-align: center;
            min-height: 48px;
        }

        .lg-status strong {
            display: block;
            font-size: 1.05rem;
            color: #111;
        }

        .lg-status span {
            display: block;
            font-size: .88rem;
            color: #8a8f98;
            margin-top: 4px;
        }

        .lg-bio-area.error .lg-status strong {
            color: #b42318;
        }

        .lg-bio-area.success .lg-status strong {
            color: #1e5c31;
        }

        .lg-secondary {
            margin-top: 14px;
            background: none;
            border: 1.5px solid #2D7D46;
            color: #2D7D46;
            border-radius: 24px;
            padding: 8px 20px;
            font-family: inherit;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .lg-secure {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #6b7280;
            font-size: .8rem;
            margin-top: 18px;
        }

        .lg-secure i {
            color: #2D7D46;
        }

        .lg-links {
            text-align: center;
            margin-top: 18px;
        }

        .lg-links a {
            color: #9ca3af;
            text-decoration: none;
        }

        .lg-version {
            text-align: center;
            margin-top: 12px;
            font-size: .85rem;
            color: #222;
        }

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

        @keyframes lg-pulse {
            0% { transform: scale(.75); opacity: .9; }
            100% { transform: scale(1.25); opacity: 0; }
        }

        @keyframes lg-scan {
            from { top: 20%; }
            to { top: 78%; }
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
                <p>سجّل الدخول إلى حسابك ببصمتك</p>
            </div>

            @if (session('success'))
                <div class="lg-alert success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="lg-alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- نموذج مخفي: يُرسل تلقائياً بعد نجاح التحقق من البصمة --}}
            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <input type="hidden" name="telegram_init_data" id="telegram_init_data">
            </form>

            <section class="lg-bio-area disabled" id="bioArea">
                <div class="lg-bio-wrap">
                    <button type="button" class="lg-bio" id="bioBtn" aria-label="الدخول بالبصمة" disabled>
                        <i class="fas fa-fingerprint" id="bioIcon"></i>
                    </button>
                </div>

                <div class="lg-status" role="status" aria-live="polite">
                    <strong id="statusTitle">جاري التحضير...</strong>
                    <span id="statusHint"></span>
                </div>

                <button type="button" class="lg-secondary" id="settingsBtn" style="display:none">
                    <i class="fas fa-cog"></i> فتح إعدادات البصمة
                </button>
            </section>

            <div class="lg-secure">
                <i class="fas fa-shield-alt"></i>
                بصمتك لا تغادر جهازك ولا يتم حفظها لدينا
            </div>

            <div class="lg-links">
                <a href="#" data-toast="للمساعدة تواصل مع خدمة العملاء">مساعدة</a>
            </div>

            <div class="lg-version">الإصدار {{ config('app.version', '1.0') }}</div>
        </main>
    </div>

    <div class="lg-toast" id="toast" role="status"></div>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        (function() {
            const hasServerError = @json($errors->any());

            const area = document.getElementById('bioArea');
            const btn = document.getElementById('bioBtn');
            const icon = document.getElementById('bioIcon');
            const title = document.getElementById('statusTitle');
            const hint = document.getElementById('statusHint');
            const settingsBtn = document.getElementById('settingsBtn');
            const form = document.getElementById('loginForm');

            let tg = null;
            let bm = null;
            let bioName = 'البصمة';
            let busy = false;

            // ===== عرض الحالة =====
            function setState(state, titleText, hintText) {
                area.className = 'lg-bio-area ' + state;
                title.textContent = titleText;
                hint.textContent = hintText || '';
                btn.disabled = state === 'disabled' || state === 'scanning' || state === 'success';
                settingsBtn.style.display = 'none';
                icon.className = state === 'success' ? 'fas fa-check' : 'fas fa-fingerprint';
            }

            function haptic(type) {
                try {
                    tg && tg.HapticFeedback && tg.HapticFeedback.notificationOccurred(type);
                } catch (e) {}
            }

            function showReady() {
                setState('ready', 'اضغط على البصمة لتسجيل الدخول', 'سنطلب منك تأكيد ' + bioName + ' المسجلة في جهازك');
            }

            // ===== إرسال بيانات تيليجرام للـ API بعد نجاح البصمة =====
            function submitLogin() {
                setState('success', 'تم التحقق بنجاح', 'جاري تسجيل الدخول...');
                haptic('success');
                document.getElementById('telegram_init_data').value = tg.initData;
                form.submit();
            }

            // ===== طلب البصمة =====
            function authenticate() {
                if (busy) return;
                busy = true;
                setState('scanning', 'ضع إصبعك على المستشعر', 'في انتظار تأكيد ' + bioName + '...');

                bm.authenticate({ reason: 'تأكيد هويتك لتسجيل الدخول' }, function(ok) {
                    busy = false;
                    if (ok) {
                        submitLogin();
                    } else {
                        haptic('error');
                        setState('error', 'لم يتم التحقق من ' + bioName, 'اضغط على البصمة للمحاولة مرة أخرى');
                    }
                });
            }

            function showDenied() {
                setState('error', 'صلاحية ' + bioName + ' مرفوضة',
                    'اسمح لتيليجرام باستخدام ' + bioName + ' من الإعدادات ثم حاول مرة أخرى');
                settingsBtn.style.display = 'inline-block';
            }

            function start() {
                if (!bm.isAccessGranted) {
                    if (bm.isAccessRequested) {
                        showDenied();
                        return;
                    }
                    bm.requestAccess({ reason: 'لتسجيل الدخول إلى حسابك بأمان باستخدام بصمتك' }, function(granted) {
                        granted ? authenticate() : showDenied();
                    });
                    return;
                }
                authenticate();
            }

            btn.addEventListener('click', start);
            settingsBtn.addEventListener('click', function() {
                bm && bm.openSettings();
            });

            // ===== التهيئة =====
            function boot() {
                tg = window.Telegram && window.Telegram.WebApp;

                if (!tg || !tg.initData) {
                    setState('disabled', 'افتح التطبيق من داخل تيليجرام', 'تسجيل الدخول بالبصمة متاح فقط عبر تطبيق تيليجرام');
                    return;
                }

                tg.ready();
                tg.expand();
                try {
                    tg.setHeaderColor('#1b2620');
                } catch (e) {}

                const name = tg.initDataUnsafe && tg.initDataUnsafe.user && tg.initDataUnsafe.user.first_name;
                if (name) document.getElementById('greeting').textContent = 'مرحباً، ' + name;

                if (!tg.isVersionAtLeast || !tg.isVersionAtLeast('7.2') || !tg.BiometricManager) {
                    setState('disabled', 'يرجى تحديث تيليجرام', 'الدخول بالبصمة يتطلب آخر إصدار من تطبيق تيليجرام');
                    return;
                }

                bm = tg.BiometricManager;

                // لو تيليجرام ما ردّ على التهيئة خلال 5 ثوانٍ لا نترك الصفحة معلّقة
                const initTimer = setTimeout(function() {
                    if (!bm.isInited) {
                        setState('error', 'تعذر تشغيل البصمة', 'أغلق التطبيق وافتحه مرة أخرى من تيليجرام');
                    }
                }, 5000);

                bm.init(function() {
                    clearTimeout(initTimer);
                    if (!bm.isBiometricAvailable) {
                        setState('disabled', 'البصمة غير متاحة على هذا الجهاز',
                            'فعّل البصمة من إعدادات الهاتف ثم أعد فتح التطبيق');
                        return;
                    }

                    if (bm.biometricType === 'face') bioName = 'بصمة الوجه';
                    showReady();

                    // طلب البصمة تلقائياً عند الفتح إذا كانت الصلاحية ممنوحة مسبقاً ولا يوجد خطأ معروض
                    if (bm.isAccessGranted && !hasServerError) authenticate();
                });
            }

            // ===== التنبيهات =====
            const toast = document.getElementById('toast');
            let toastTimer;
            document.querySelectorAll('[data-toast]').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    toast.textContent = el.dataset.toast;
                    toast.classList.add('show');
                    clearTimeout(toastTimer);
                    toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
                });
            });

            // عند الرجوع للصفحة من سجل المتصفح نعيد الحالة الجاهزة
            window.addEventListener('pageshow', function(e) {
                if (e.persisted && bm && bm.isBiometricAvailable) showReady();
            });

            window.lgBoot = boot; // يساعد في الاختبار من أدوات المطور
            document.addEventListener('DOMContentLoaded', boot);
        })();
    </script>
@endsection
