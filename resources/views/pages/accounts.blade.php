@extends('layouts.app')
@section('title', 'حساباتي')

@section('content')
    <div class="page-header">
        <a href="{{ route('dashboard') }}" class="back-btn" aria-label="رجوع"><i class="fas fa-arrow-right"></i></a>
        <h2>حساباتي</h2>
        <div class="header-spacer"></div>
    </div>

    <div class="page-content">
        @forelse (session('accounts', []) as $account)
            <div class="info-card">
                <div class="account-row">
                    <div class="acc-icon"><i class="fas fa-wallet"></i></div>
                    <div class="acc-main">
                        <div class="acc-no">{{ $account['accountNo'] ?? '' }}</div>
                        <div class="acc-type">{{ $account['ledModelDesc'] ?? '' }}</div>
                    </div>
                </div>

                <div class="account-row-footer">
                    <div>
                        <div class="bal-label">الرصيد المتوفر</div>
                        <div class="bal-value">{{ $account['availBal'] ?? '0.00' }} SDG</div>
                    </div>
                    <span class="badge badge-success">
                        <span class="status-dot status-active"></span>
                        {{ $account['accountStatus'] ?? '' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="icon"><i class="fas fa-wallet"></i></div>
                <p>لا توجد حسابات لعرضها</p>
            </div>
        @endforelse
    </div>
@endsection
