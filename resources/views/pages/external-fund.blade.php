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

            @if ($errors->any())
                <div class="alert alert-error" style="margin: 15px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div id="ajax-error" class="alert alert-error" style="display:none; margin: 15px;"></div>

            <div class="info-card">
                <form method="POST" action="{{ route('transfer-internal.submit') }}" id="transfer-form">
                    @csrf

                    <div class="form-group">
                        <label for="from_account">من حساب</label>
                        <select id="from_account" name="from_account" class="form-control">
                            @foreach ($accounts as $account)
                                <option value="{{ $account['accountNo'] }}"
                                    data-balance="{{ number_format($account['availBal'] ?? 0, 2) }}">
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
                        <input type="text" id="bban" name="bban" class="form-control"
                            placeholder="أدخل رقم الحساب أو الرقم التعريفي" required>
                    </div>

                    <button type="button" id="verify-btn" class="btn btn-dark-custom">التحقق</button>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="customer_name">اسم العميل</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control"
                            placeholder="اسم العميل المستفيد" readonly>
                    </div>

                    <!-- تم إزالة الكلاس المعطل واستبداله بالتحكم البرمجي -->
                    <div id="remaining-fields">
                        <div class="form-group">
                            <label for="to_account">إلى حساب المستفيد</label>
                            <select id="to_account" name="to_account" class="form-control" disabled required>
                                <option value="">-- اختر حساب المستفيد --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">رقم الهاتف المحمول (اختياري)</label>
                            <input type="text" id="phone" name="phone" class="form-control"
                                placeholder="أدخل رقم الهاتف المحمول">
                        </div>

                        <div class="form-group">
                            <label for="note">التعليق (اختياري)</label>
                            <input type="text" id="note" name="note" class="form-control"
                                placeholder="أدخل التعليق">
                        </div>

                        <div class="form-group">
                            <label for="amount">المبلغ</label>
                            <input type="number" id="amount" name="amount" class="form-control"
                                placeholder="أدخل المبلغ" min="1" required>
                        </div>

                        <button type="submit" id="submit-transfer-btn" class="btn btn-dark-custom"
                            style="margin-top: 10px;">
                            تحويل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* ... باقي الـ CSS الخاص بك كما هو ... */
        .page-container {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            direction: rtl;
        }

        .page-header {
            background-color: #ffffff;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e9ecef;
        }

        .info-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #2d3748;
            border-radius: 10px;
        }

        .btn-dark-custom {
            width: 100%;
            background-color: #4a5568;
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .balance-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateBalance() {
                let selectedOption = $('#from_account option:selected');
                $('#available-balance').text((selectedOption.data('balance') || '0.00') + ' SDG');
            }

            updateBalance();
            $('#from_account').on('change', updateBalance);

            $('#verify-btn').click(function() {
                let customerNum = $('#bban').val().trim();
                let btn = $(this);

                if (customerNum == "") {
                    alert("الرجاء إدخال رقم الحساب");
                    return;
                }

                btn.prop('disabled', true).text('جاري التحقق...');
                $('#ajax-error').hide();
                $('#to_account').html('<option value="">-- اختر حساب المستفيد --</option>').prop('disabled',
                    true);

                $.ajax({
                    url: `/bank-api/customer/name/${customerNum}`,
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                        "Accept": "application/json"
                    },
                    success: function(response) {
                        if (response.success && response.name) {
                            $('#customer_name').val(response.name);
                            if (response.accounts && response.accounts.length > 0) {
                                $.each(response.accounts, function(index, acc) {
                                    $('#to_account').append(
                                        `<option value="${acc.accountNo}">${acc.ledModelDesc} - ${acc.accountNo}</option>`
                                        );
                                });
                                $('#to_account').prop('disabled', false); // تفعيل القائمة هنا
                            } else {
                                $('#ajax-error').text("لا توجد حسابات لهذا العميل").show();
                            }
                        } else {
                            $('#ajax-error').text("العميل غير موجود").show();
                        }
                        btn.prop('disabled', false).text('التحقق');
                    },
                    error: function() {
                        $('#ajax-error').text("خطأ في الاتصال بالسيرفر").show();
                        btn.prop('disabled', false).text('التحقق');
                    }
                });
            });
        });
    </script>
@endsection
