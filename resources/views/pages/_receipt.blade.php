{{--
    إيصال موحّد للتحويلات
    المتغيرات: $amount (رقم) و $rows (مصفوفة: [العنوان, القيمة, ltr؟])
--}}
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn" aria-label="الرئيسية"><i class="fas fa-arrow-right"></i></a>
    <h2>ملخص الإشعار</h2>
    <div class="header-spacer"></div>
</div>

<div class="page-content">
    <div class="receipt-card" id="receipt">
        <div class="receipt-top">
            <div class="receipt-check"><i class="fas fa-check"></i></div>
            <h3>تمت العملية بنجاح</h3>
            <div class="receipt-amount">{{ number_format((float) $amount, 2) }} <small>SDG</small></div>
        </div>

        <div class="receipt-rows">
            @foreach ($rows as [$label, $value, $ltr])
                <div class="receipt-row">
                    <span class="k">{{ $label }}</span>
                    <span class="v {{ $ltr ? 'ltr' : '' }}">{{ filled($value) ? $value : '-----' }}</span>
                </div>
            @endforeach
        </div>

        <div class="receipt-brand">
            <span class="mini-ring" aria-hidden="true"></span>
            بنك ام درمان الوطني
        </div>
    </div>

    <div class="receipt-actions">
        <button type="button" class="btn btn-outline" id="shareBtn">
            <i class="fas fa-share-alt"></i> مشاركة
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </div>
</div>

<script>
    document.getElementById('shareBtn').addEventListener('click', async function() {
        const rows = [...document.querySelectorAll('#receipt .receipt-row')]
            .map(r => r.querySelector('.k').textContent.trim() + ': ' + r.querySelector('.v').textContent.trim());
        const text = 'إشعار تحويل - بنك ام درمان الوطني\n' +
            'المبلغ: ' + document.querySelector('.receipt-amount').textContent.trim() + '\n' + rows.join('\n');

        try {
            if (navigator.share) {
                await navigator.share({ title: 'إشعار تحويل', text: text });
            } else {
                await navigator.clipboard.writeText(text);
                this.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
            }
        } catch (e) {}
    });
</script>
