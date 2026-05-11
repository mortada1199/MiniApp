@extends('layouts.app')
@section('title', 'الخدمات الرئيسية')

@section('content')
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

<!-- Account Card -->
<div class="account-card-wrapper">
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



{{-- 
        <a href="#" class="service-card">
            <div class="service-icon icon-payment">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span class="label">دفع الفواتير</span>
        </a> --}}

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
