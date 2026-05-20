@extends('layouts.app')
@section('title', 'التحويل الي عميل داخل البنك')

@section('content')
    <div class="page-container">
        <div class="page-header">
            <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
            <h2>التحويل الي عميل داخل البنك</h2>
        </div>

        <div class="page-content fade-in">
            <div id="ajax-error" class="alert alert-error" style="display:none; margin: 15px; padding: 10px; background: #fee; color: #cc0000; border: 1px solid #cc0000;"></div>

            <div class="info-card">
                <form method="POST" action="{{ route('transfer-internal.submit') }}" id="transfer-form">
                    @csrf
                    <div class="form-group">
                        <label for="from_account">من حساب</label>
                        <select id="from_account" name="from_account" class="form-control">
                            @foreach ($accounts as $account)
                                <option value="{{ $account['accountNo'] }}" data-balance="{{ number_format($account['availBal'] ?? 0, 2) }}">
                                    {{ $account['ledModelDesc'] }} - {{ $account['accountNo'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="balance-box">
                        <span class="label">الرصيد المتوفر</span>
                        <span class="amount" id="available-balance">0.00 SDG</span>
                    </div>

                    <div class="form-group">
                        <label for="bban">رقم الحساب</label>
                        <input type="text" id="bban" name="bban" class="form-control" placeholder="أدخل رقم الحساب أو الرقم التعريفي" required>
                    </div>

                    <button type="button" id="verify-btn" class="btn btn-dark-custom">التحقق</button>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="customer_name">اسم العميل</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="اسم العميل المستفيد" readonly>
                    </div>

                    <div id="remaining-fields">
                        <div class="form-group">
                            <label for="to_account">إلى حساب المستفيد</label>
                            <select id="to_account" name="to_account" class="form-control" disabled required>
                                <option value="">-- اختر حساب المستفيد --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">المبلغ</label>
                            <input type="number" id="amount" name="amount" class="form-control" placeholder="أدخل المبلغ" min="1" required>
                        </div>
                        <button type="submit" id="submit-transfer-btn" class="btn btn-dark-custom">تحويل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#verify-btn').click(function() {
            let customerNum = $('#bban').val().trim();
            let btn = $(this);

            if (customerNum == "") { alert("الرجاء إدخال رقم الحساب"); return; }

            btn.prop('disabled', true).text('جاري التحقق...');
            $('#ajax-error').hide();
            $('#to_account').html('<option value="">-- اختر حساب المستفيد --</option>').prop('disabled', true);

            $.ajax({
                url: `/bank-api/customer/name/${customerNum}`,
                method: "GET",
                headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val(), "Accept": "application/json" },
                success: function(response) {
                    // 1. طباعة الرد في الكونسول
                    console.log("البيانات القادمة من السيرفر:", response);
                    
                    // 2. إظهار تنبيه على الهاتف لمراجعة البيانات
                    alert("البيانات القادمة: " + JSON.stringify(response));

                    if (response.success && response.name) {
                        $('#customer_name').val(response.name);
                        
                        // فحص المصفوفة
                        if (response.accounts && response.accounts.length > 0) {
                            $.each(response.accounts, function(index, acc) {
                                $('#to_account').append(`<option value="${acc.accountNo}">${acc.ledModelDesc} - ${acc.accountNo}</option>`);
                            });
                            $('#to_account').prop('disabled', false);
                        } else {
                            $('#ajax-error').text("تم العثور على العميل، لكن لا توجد حسابات مرتبطة به.").show();
                        }
                    } else {
                        $('#ajax-error').text("العميل غير موجود أو هناك خطأ في البيانات.").show();
                    }
                    btn.prop('disabled', false).text('التحقق');
                },
                error: function(xhr) {
                    alert("خطأ في الاتصال بالسيرفر. الحالة: " + xhr.status);
                    $('#ajax-error').text("خطأ في الاتصال").show();
                    btn.prop('disabled', false).text('التحقق');
                }
            });
        });
    });
</script>
@endsection