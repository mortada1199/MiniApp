<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="بنك ام درمان الوطني">
    <title>@yield('title', 'الخدمات المصرفية')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- داخل الـ <head> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>
    <div class="app-container">
        @yield('content')
    </div>

    <!-- قبل إغلاق الـ </body> -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @yield('scripts')

    <!-- كود الخروج التلقائي عند الخمول لمدة دقيقة -->
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let timeInactivityLimit = 60 * 1000; // دقيقة واحدة بالملي ثانية
                let timeoutTimer;

                // دالة إرسال طلب تسجيل الخروج بأمان عبر POST
                function logoutUser() {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('logout') }}"; // تأكد أن هذا اسم راوت الـ logout عندك

                    let csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";

                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }

                // دالة إعادة تصفير العداد عند رصد حركة
                function resetTimer() {
                    clearTimeout(timeoutTimer);
                    timeoutTimer = setTimeout(logoutUser, timeInactivityLimit);
                }

                // مراقبة كافة تحركات المستخدم (الماوس، الكيبورد، اللمس على الهواتف، التمرير)
                window.onload = resetTimer;
                document.onmousemove = resetTimer;
                document.onkeydown = resetTimer;
                document.onkeyup = resetTimer;
                document.onmousedown = resetTimer;
                document.onclick = resetTimer;
                document.onscroll = resetTimer;
                document.addEventListener('touchstart', resetTimer); // مهم جداً لمستخدمي الهواتف
            });
        </script>
    @endauth
</body>

</html>
