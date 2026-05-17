@extends('layouts.app')
@section('title', 'حساباتي')

@section('content')
    <div class="page-header">
        <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
        <h2>حساباتي</h2>
        <div style="width:36px"></div>
    </div>

    <div class="page-content fade-in">
        @foreach (session('accounts') as $account)
            <div class="info-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span class="badge badge-success"> {{ $account['accountStatus'] }}</span>
                    <span class="badge badge-info">{{ $account['availBal'] }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div
                        style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#2D7D46,#1e5c31);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;flex-shrink:0;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--text);direction:ltr;text-align:right;">
                            {{ $account['accountNo'] }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);"> {{ $account['ledModelDesc'] }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
