@extends('layouts.app')
@section('title', 'التحويل الي عميل داخل البنك')

@section('content')
    <div class="page-container">
        <div class="page-header">
            <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
            <h2>التحويل الى عميل داخل البنك</h2>
            <div class="header-spacer"></div>
        </div>

        <div class="page-content">
            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div id="ajax-error" class="alert alert-error" style="display:none;"></div>

            <div class="info-card">
                <h3><i class="fas fa-user-friends"></i> بيانات التحويل</h3>
                <form method="POST" action="{{ route('transfer-internal.submit') }}" id="transfer-form">
                    @csrf
                    <!-- اختيار الحساب المرسل -->
                    <div class="form-group">
                        <label for="from_account">من حساب</label>
                        <select id="from_account" name="from_account" class="form-control">
                            @foreach ($accounts as $account)
                                <option value="{{ $account['accountNo'] }}" data-balance="{{ $account['availBal'] ?? 0 }}">
                                    {{ $account['ledModelDesc'] }} - {{ $account['accountNo'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الرصيد -->
                    <div class="balance-box">
                        <span class="label">الرصيد المتوفر</span>
                        <span class="amount" id="available-balance">0.00 SDG</span>
                    </div>

                    <div class="form-group">
                        <label for="bban">رقم حساب المستفيد</label>
                        <input type="text" id="bban" name="bban" class="form-control" placeholder="أدخل رقم الحساب" required>
                    </div>

                    <button type="button" id="verify-btn" class="btn btn-dark-custom">
                        <i class="fas fa-search"></i> التحقق
                    </button>

                    <div class="form-group" style="margin-top: 16px;">
                        <label for="customer_name">اسم العميل</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" readonly>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> تحويل
                        </button>
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
        // تحديث رصيد المرسل
        function updateBalance() {
            let selectedOption = $('#from_account option:selected');
            let balance = selectedOption.data('balance');
            let formattedBalance = (parseFloat(String(balance).replace(/,/g, '')) || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
            $('#available-balance').text(formattedBalance + ' SDG');
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
                    // التعديل هنا: استخدام response.customerAccounts بدلاً من response.accounts
                    if (response.success && response.name) {
                        $('#customer_name').val(response.name);
                        
                        if (response.customerAccounts && response.customerAccounts.length > 0) {
                            $.each(response.customerAccounts, function(index, acc) {
                                $('#to_account').append(`<option value="${acc.accountNo}">${acc.ledModelDesc} - ${acc.accountNo}</option>`);
                            });
                            $('#to_account').prop('disabled', false);
                        } else {
                            $('#ajax-error').text("تم العثور على العميل، لكن لا توجد حسابات له.").show();
                        }
                    } else {
                        $('#ajax-error').text("العميل غير موجود").show();
                    }
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i> التحقق');
                },
                error: function(xhr) {
                    // أظهر سبب الخطأ الحقيقي من السيرفر (رمز الحالة + الرسالة) لتسهيل المعرفة
                    let msg = 'خطأ في الاتصال بالسيرفر';
                    if (xhr.responseJSON) {
                        msg = xhr.responseJSON.message || xhr.responseJSON.debug_error || msg;
                    } else if (xhr.responseText) {
                        msg = xhr.responseText.substring(0, 200);
                    }
                    $('#ajax-error').text(msg + ' (كود ' + xhr.status + ')').show();
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i> التحقق');
                }
            });
        });
    });
</script>
@endsection