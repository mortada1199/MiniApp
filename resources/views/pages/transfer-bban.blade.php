@extends('layouts.app')
@section('title', 'تحويل BBAN')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
    <h2>تحويل BBAN</h2>
    <div style="width:36px"></div>
</div>

<div class="page-content fade-in">
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    
    <div id="ajax-error" class="alert alert-error" style="display:none;"></div>

    <div class="info-card">
        <h3><i class="fas fa-credit-card"></i> تحويل إلى BBAN</h3>
        <form method="POST" action="#" id="transfer-form">
            @csrf
            
            <div class="form-group">
                <label for="from_account">من حساب</label>
                <select id="from_account" name="from_account" class="form-control">
                    <option value="0210 0278242 00131 01000">0210 0278242 00131 01000 - ادخار</option>
                    <option value="0210 0278242 00132 01000">0210 0278242 00132 01000 - جاري</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bban">رقم BBAN المستفيد</label>
                <input type="text" id="bban" name="bban" class="form-control" placeholder="أدخل رقم BBAN" required>
            </div>

            <button type="button" id="verify-btn" class="btn btn-secondary" style="width: 100%; margin-bottom: 15px; background: #4a4a4a; color: white;">التحقق</button>

            <div id="receiver-info" style="display: none;">
                <div class="form-group">
                    <label>اسم العميل</label>
                    <input type="text" id="customer_name" class="form-control" style="background: #e9ecef;" readonly>
                </div>

                <div class="form-group">
                    <label>بنك المستفيد</label>
                    <input type="text" id="bank_name" class="form-control" style="background: #e9ecef;" readonly>
                </div>

                <div class="form-group">
                    <label>الحد الأدنى - الأعلى للمبلغ</label>
                    <input type="text" id="transfer_limits" class="form-control" style="background: #e9ecef;" readonly>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف المحمول (اختياري)</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="أدخل رقم الهاتف">
                </div>

                <div class="form-group">
                    <label for="note">التعليق (اختياري)</label>
                    <input type="text" id="note" name="note" class="form-control" placeholder="أدخل ملاحظات">
                </div>

                <div class="form-group">
                    <label for="amount">المبلغ</label>
                    <input type="number" id="amount" name="amount" class="form-control" placeholder="أدخل المبلغ" min="1">
                </div>

                <button type="submit" class="btn btn-primary" style="background: linear-gradient(to right, #5c8e58, #94b476); border: none;">
                    <i class="fas fa-paper-plane"></i> تحويل
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#verify-btn').click(function() {
        let bban = $('#bban').val();
        let btn = $(this);

        if(bban == "") {
            alert("الرجاء إدخال رقم البيبان");
            return;
        }

        // تحويل الزر لحالة التحميل
        btn.prop('disabled', true).text('جاري التحقق...');
        $('#ajax-error').hide();

        $.ajax({
            url: "{{ route('transfer-bban.verify') }}", // تأكد من إنشاء هذا المسار في الـ Routes
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                bban: bban
            },
            success: function(response) {
                if(response.success) {
                    // تعبئة البيانات وإظهار القسم المخفي
                    $('#customer_name').val(response.data.name);
                    $('#bank_name').val(response.data.bank);
                    $('#transfer_limits').val(response.data.limits);
                    
                    $('#receiver-info').slideDown(); // إظهار باقي الفورم بسلاسة
                    btn.hide(); // إخفاء زر التحقق بعد النجاح
                } else {
                    $('#ajax-error').text("رقم البيبان غير صحيح").show();
                    btn.prop('disabled', false).text('التحقق');
                }
            },
            error: function() {
                $('#ajax-error').text("حدث خطأ في الاتصال بالسيرفر").show();
                btn.prop('disabled', false).text('التحقق');
            }
        });
    });
});
</script>
@endsection