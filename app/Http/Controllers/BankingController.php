@extends('layouts.app')
@section('title', 'التحويل الي عميل داخل البنك')

@section('content')
    <div class="page-container">
        <div class="page-header">
            <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
            <h2>التحويل الي عميل داخل البنك</h2>
            <div style="width:36px"></div>
        </div>

        <div class="page-content fade-in">
            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

            <div id="ajax-error" class="alert alert-error" style="display:none; margin: 15px;"></div>

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
                        <input type="text" id="bban" name="bban" class="form-control" placeholder="أدخل رقم الحساب" required>
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
        function updateBalance() {
            let selected = $('#from_account option:selected');
            $('#available-balance').text((selected.data('balance') || '0.00') + ' SDG');
        }
        $('#from_account').on('change', updateBalance);
        updateBalance();

        $('#verify-btn').click(function() {
            let bban = $('#bban').val().trim();
            let btn = $(this);
            
            if (!bban) { alert("الرجاء إدخال رقم الحساب"); return; }

            btn.prop('disabled', true).text('جاري التحقق...');
            $('#ajax-error').hide();
            $('#to_account').html('<option value="">-- اختر حساب المستفيد --</option>').prop('disabled', true);

            $.ajax({
                url: `/bank-api/customer/name/${bban}`,
                method: "GET",
                success: function(response) {
                    // في حال كان هناك مشكلة، هذا التنبيه سيظهر لك البيانات على شاشة الجوال
                    if (response.success && response.name) {
                        $('#customer_name').val(response.name);
                        
                        if (response.accounts && response.accounts.length > 0) {
                            $.each(response.accounts, function(i, acc) {
                                $('#to_account').append(`<option value="${acc.accountNo}">${acc.ledModelDesc} - ${acc.accountNo}</option>`);
                            });
                            $('#to_account').prop('disabled', false);
                        } else {
                            alert("البيانات المستلمة: " + JSON.stringify(response));
                            $('#ajax-error').text("تم العثور على العميل ولكن مصفوفة الحسابات فارغة.").show();
                        }
                    } else {
                        alert("فشل التحقق، الرد هو: " + JSON.stringify(response));
                        $('#ajax-error').text("فشل التحقق: العميل غير موجود.").show();
                    }
                    btn.prop('disabled', false).text('التحقق');
                },
                error: function(xhr) {
                    alert("خطأ في الاتصال: " + xhr.status);
                    $('#ajax-error').text("خطأ في الاتصال بالسيرفر.").show();
                    btn.prop('disabled', false).text('التحقق');
                }
            });
        });
    });
</script>
@endsection