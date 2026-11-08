@extends('layouts.app')
@section('title', 'كشف الحساب')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
    <h2>كشف الحساب</h2>
    <div style="width:36px"></div>
</div>

<div class="page-content fade-in">
    <div class="info-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الوصف</th>
                        <th>المبلغ</th>
                        <th>الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td style="direction:ltr; text-align:right; white-space:nowrap;">{{ $tx['date'] }}</td>
                        <td>{{ $tx['description'] }}</td>
                        <td style="direction:ltr; text-align:right; font-weight:700; color:{{ str_starts_with($tx['amount'], '+') ? '#16a34a' : '#dc2626' }};">
                            {{ $tx['amount'] }}
                        </td>
                        <td style="direction:ltr; text-align:right;">{{ $tx['balance'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
