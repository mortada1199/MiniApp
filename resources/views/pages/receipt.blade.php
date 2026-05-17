@extends('layouts.app')
@section('title', 'ملخص الإشعار')

@section('content')
<style>
    /* تنسيق الحاوية الرئيسية لصفحة الإشعار */
    .receipt-container {
        font-family: 'Cairo', sans-serif;
        background-color: #f7f7f7;
        padding: 15px;
        direction: rtl;
        min-height: 100vh;
    }

    /* هيدر الصفحة */
    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        margin-bottom: 20px;
    }
    .receipt-header h2 {
        font-size: 20px;
        color: #333;
        margin: 0;
        font-weight: bold;
    }
    .receipt-header .icon-btn {
        background: none;
        border: none;
        font-size: 18px;
        color: #333;
        cursor: pointer;
    }

    /* كارد الشعار العلوي */
    .brand-card {
        background: #f1f2ee;
        border: 1px solid #d4d7cd;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .brand-logo-placeholder {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 28px;
        font-weight: bold;
        color: #1a5b33; /* لون O-cash الأخضر */
    }
    .brand-logo-placeholder span {
        color: #0c3457; /* اللون الأزرق في الشعار */
    }
    .brand-tagline {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    /* كارد تفاصيل الحركة */
    .details-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    
    /* شريط تفاصيل الحركة الأخضر في الأعلى */
    .details-title-bar {
        background: #f4f6f0;
        border-bottom: 1px solid #e0e4d6;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }
    .status-badge {
        color: #2d8a4e;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 18px;
    }

    /* قائمة البيانات */
    .data-list {
        padding: 20px 15px;
    }
    .data-item {
        text-align: center;
        margin-bottom: 18px;
    }
    .data-item:last-child {
        margin-bottom: 0;
    }
    .data-label {
        font-size: 14px;
        color: #333;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .data-value {
        font-size: 16px;
        color: #000;
        font-weight: bold;
        word-break: break-all;
    }
    
    /* تنسيق خاص بحقل المبلغ ليظهر بشكل بارز ومميز */
    .amount-value {
        font-size: 20px;
        color: #111;
        font-weight: 800;
    }
</style>

<div class="receipt-container">
    
    <div class="receipt-header">
        <button class="icon-btn" onclick="window.history.back()">
            <i class="fas fa-chevron-right"></i>
        </button>
        <h2>ملخص الإشعار</h2>
        <button class="icon-btn">
            <i class="fas fa-share-alt"></i>
        </button>
    </div>

    <div class="brand-card">
        <div class="brand-logo-placeholder">
            <i class="fas fa-circle-notch"></i> O-<span>cash</span>
        </div>
        <div class="brand-tagline">لكل الناس</div>
    </div>

    <div class="details-card">
        <div class="details-title-bar">
            <span>تفاصيل الحركة</span>
            <div class="status-badge">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="data-list">
            <div class="data-item">
                <div class="data-label">رقم الحركة</div>
                <div class="data-value">{{ $transaction['reference_no'] ?? '2605161145410895555' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">تاريخ الحركة</div>
                <div class="data-value">{{ $transaction['date'] ?? '16/05/2026 11:45:42 AM' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">نوع الحركة</div>
                <div class="data-value">{{ $transaction['type'] ?? 'التحويل الى حساب مصرفي آخر' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">من حساب</div>
                <div class="data-value">{{ $transaction['from_account'] ?? '022408955550013101000' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">إلى حساب</div>
                <div class="data-value">{{ $transaction['to_account'] ?? '022612952290013101000' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">اسم العميل</div>
                <div class="data-value">{{ $transaction['client_name'] ?? 'زينب سعيد عبدالله سعد' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">رقم الهاتف المحمول</div>
                <div class="data-value">{{ $transaction['phone'] ?? '-----' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">تعليقات</div>
                <div class="data-value">{{ $transaction['notes'] ?? '-----' }}</div>
            </div>

            <div class="data-item">
                <div class="data-label">المبلغ</div>
                <div class="data-value amount-value">{{ number_format($transaction['amount'] ?? 50000) }} SDG</div>
            </div>
        </div>
    </div>
</div>
@endsection