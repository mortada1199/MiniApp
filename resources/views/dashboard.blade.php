@extends('layouts.app')
@section('title', 'الخدمات الرئيسية')

@section('content')
<style>
    /* ضبط الحاوية لتأخذ العرض الكامل */
    .account-card-wrapper {
        width: 100%;
        overflow: hidden;
        position: relative;
        margin-top: 10px;
        margin-bottom: -10px;
        padding-bottom: 25px;
    }
    
    .swiper-wrapper {
        display: flex !important;
    }

    .swiper-slide {
        width: 100% !important;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        /* إضافة padding جانبي بسيط ليشبه image_57d55c.png */
        padding: 0 15px; 
        box-sizing: border-box;
    }

    /* جعل البطاقة تمتد لتملأ السلايد */
    .account-card {
        width: 100%; /* تمدد لتأخذ عرض السلايد */
        max-width: 100%; /* ضمان عدم الانكماش */
        margin: 0 auto;
        box-sizing: border-box;
    }

    .swiper-pagination {
        bottom: 5px !important;
    }
</style>

<!-- Header -->
<div class="app-header">
    <a href="{{ route('logout') }}" class="header-icon" title="خروج">
        <i class="fas fa-sign-out-alt"></i>
    </a>
    <h1>الخدمات الرئيسية</h1>
    <button class="header-icon">
        <i class="fas fa-th"></i>
    </button>
</div>

<!-- User Greeting -->
<div class="user-greeting">
    <div class="user-info">
        <div class="name">اهلا بك murtada</div>
        <div class="last-login">آخر تسجيل دخول ناجح : 2026</div>
    </div>
    <div class="user-avatar">
        <i class="fas fa-user"></i>
    </div>
</div>

<!-- Account Card Slider -->
<div class="account-card-wrapper swiper">
    <div class="swiper-wrapper">
        
        <!-- البطاقة الأولى -->
        <div class="swiper-slide">
            <div class="account-card">
                <div class="card-top">
                    <div class="card-type">ادخار</div>
                    <div class="card-logo"><i class="fas fa-landmark"></i></div>
                </div>
                <div class="card-number">20207774280001</div>
                <div class="card-balances">
                    <div class="balance-item">
                        <div class="label">الرصيد الحالي</div>
                        <div class="value">10</div>
                    </div>
                    <div class="balance-item">
                        <div class="label">الرصيد المتوفر</div>
                        <div class="value">20</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- البطاقة الثانية -->
        <div class="swiper-slide">
            <div class="account-card">
                <div class="card-top">
                    <div class="card-type">جاري</div>
                    <div class="card-logo"><i class="fas fa-landmark"></i></div>
                </div>
                <div class="card-number">20207774289999</div>
                <div class="card-balances">
                    <div class="balance-item">
                        <div class="label">الرصيد الحالي</div>
                        <div class="value">500</div>
                    </div>
                    <div class="balance-item">
                        <div class="label">الرصيد المتوفر</div>
                        <div class="value">450</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="swiper-pagination"></div>
</div>

<!-- Services Grid -->
<div class="services-section">
    <div class="services-grid">
        <a href="{{ route('internal-fund') }}" class="service-card">
            <div class="service-icon icon-transfer">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <span class="label">تحويل داخلي</span>
        </a>

        <a href="{{ route('accounts') }}" class="service-card">
            <div class="service-icon icon-accounts">
                <i class="fas fa-users"></i>
            </div>
            <span class="label">حساباتي</span>
        </a>

        <a href="{{ route('transfer-bban') }}" class="service-card">
            <div class="service-icon icon-visa">
                <i class="fas fa-credit-card"></i>
            </div>
            <span class="label">تحويل BBAN</span>
        </a>

        <a href="#" class="service-card">
            <div class="service-icon icon-subscription">
                <i class="fas fa-list-alt"></i>
            </div>
            <span class="label">كشف حساب</span>
        </a>
    </div>
</div>

<div class="bottom-spacer"></div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper('.account-card-wrapper', {
            slidesPerView: 1, 
            spaceBetween: 0,
            centeredSlides: true,
            autoHeight: true, 
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    });
</script>
@endsection