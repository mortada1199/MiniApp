@extends('layouts.app')
@section('title', 'الخدمات الرئيسية')

@php
    $accounts = session('accounts', []);
    $customerName = data_get($accounts, '0.cusShoName', 'عميلنا العزيز');
    $loginAt = session('login_at');
@endphp

@section('content')

    <style>
        .account-card-wrapper {
            width: 100%;
            overflow: hidden;
            position: relative;
            padding-bottom: 26px;
        }

        .swiper-wrapper {
            display: flex !important;
        }

        .swiper-slide {
            width: 100% !important;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            padding: 0 2px;
            box-sizing: border-box;
        }

        .account-card {
            width: 100%;
            margin: 0 auto;
        }

        .swiper-pagination {
            bottom: 4px !important;
        }
    </style>

    <!-- Header -->
    <div class="app-header">

        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="header-icon" title="خروج" aria-label="تسجيل الخروج">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>

        <h1>الخدمات الرئيسية</h1>

        <a href="{{ route('accounts') }}" class="header-icon" title="حساباتي" aria-label="حساباتي">
            <i class="fas fa-th-large"></i>
        </a>

        <!-- User Greeting -->
        <div class="user-greeting">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>

            <div class="user-info">
                <div class="hello">أهلاً بك 👋</div>
                <div class="name">{{ $customerName }}</div>
                @if ($loginAt)
                    <div class="last-login">
                        <i class="far fa-clock"></i>
                        تسجيل الدخول: {{ \Illuminate\Support\Carbon::parse($loginAt)->format('Y/m/d h:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="sheet">

        <!-- Account Card Slider -->
        @if (count($accounts))
            <div class="account-card-wrapper swiper">
                <div class="swiper-wrapper">
                    @foreach ($accounts as $account)
                        <div class="swiper-slide">
                            <div class="account-card">
                                <div class="card-top">
                                    <div class="card-type">
                                        {{ $account['ledModelDesc'] ?? 'حساب' }}
                                    </div>
                                    <div class="card-logo">
                                        <i class="fas fa-landmark"></i>
                                    </div>
                                </div>

                                <div class="card-number">
                                    {{ $account['accountNo'] ?? '' }}
                                </div>

                                <div class="card-balances">
                                    <div class="balance-item">
                                        <div class="label">الرصيد الحالي</div>
                                        <div class="value gold">{{ $account['availBal'] ?? '0.00' }} <small>SDG</small></div>
                                    </div>

                                    <div class="balance-item">
                                        <div class="label">الحالة</div>
                                        <div class="value">{{ $account['accountStatus'] ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="swiper-pagination"></div>
            </div>
        @endif

        <!-- Services Grid -->
        <div class="services-section">
            <div class="section-title">الخدمات</div>

            <div class="services-grid">
                <a href="{{ route('internal-fund') }}" class="service-card">
                    <div class="service-icon icon-transfer">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <span class="label">تحويل بين حساباتي</span>
                </a>

                <a href="{{ route('accounts') }}" class="service-card">
                    <div class="service-icon icon-accounts">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="label">حساباتي</span>
                </a>

                <a href="{{ route('transfer-internal') }}" class="service-card">
                    <div class="service-icon icon-visa">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <span class="label">تحويل لعميل داخل البنك</span>
                </a>

                <a href="{{ route('transfer-bban') }}" class="service-card">
                    <div class="service-icon icon-visa">
                        <i class="fas fa-university"></i>
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
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.account-card-wrapper')) {
                new Swiper('.account-card-wrapper', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    centeredSlides: true,
                    autoHeight: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                });
            }
        });
    </script>
@endsection
