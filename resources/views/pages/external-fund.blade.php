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
                                    data-balance="{{ number_format($account['balance'] ?? 0, 2) }}">
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
                            placeholder="أدخل رقم الحساب" required>
                    </div>

                    <button type="button" id="verify-btn" class="btn btn-dark-custom">التحقق</button>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="customer_name">اسم العميل</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control"
                            placeholder="اسم العميل المستفيد" readonly>
                    </div>

                    {{-- <div class="switch-container">
                        <span class="switch-label">اضافة العميل الى قائمة المستفيدين</span>
                        <input type="checkbox" id="add_to_beneficiaries" name="add_to_beneficiaries"
                            style="width: 20px; height: 20px; accent-color: #2d8a4e;">
                    </div> --}}

                    <div id="remaining-fields" class="disabled-group">

                        <div class="form-group">
                            <label for="to_account">إلى الحساب</label>
                            <select id="to_account" name="to_account" class="form-control">
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
                                placeholder="أدخل المبلغ" min="1">
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
        /* التنسيقات المطابقة لتصميم التطبيق الفعلي */
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

        .page-header h2 {
            font-size: 18px;
            color: #333;
            margin: 0;
            font-weight: bold;
        }

        .back-btn {
            color: #333;
            font-size: 20px;
            text-decoration: none;
        }

        .info-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #888;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #2d3748;
            border-radius: 10px;
            font-size: 15px;
            color: #333;
            background-color: #fff;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #2d8a4e;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #e2e8f0;
            color: #4a5568;
        }

        .disabled-group {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .btn-dark-custom {
            width: 100%;
            background-color: #4a5568;
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-dark-custom:hover {
            background-color: #313b4e;
        }

        .btn-dark-custom:disabled {
            background-color: #a0aec0;
            cursor: not-allowed;
        }

        .balance-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            text-align: left;
        }

        .balance-box .label {
            font-size: 13px;
            color: #718096;
            display: block;
        }

        .balance-box .amount {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        .switch-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 5px 0;
        }

        .switch-label {
            font-size: 15px;
            color: #2d3748;
            font-weight: 600;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // تحديث الرصيد المتوفر عند تغيير الحساب المختار من القائمة
            function updateBalance() {
                let selected = $('#from_account option:selected');
                let balance = selected.data('balance') || '0.00';
                $('#available-balance').text(balance + ' SDG');
            }
            $('#from_account').change(updateBalance);
            updateBalance();

            // عند الضغط على زر التحقق
            $('#verify-btn').click(function() {
                let customerNum = $('#bban').val().trim();
                let btn = $(this);

                if (customerNum == "") {
                    alert("الرجاء إدخال رقم الحساب للتحقق");
                    return;
                }

                // تحويل الزر لحالة التحميل وتجهيز الواجهة
                btn.prop('disabled', true).text('جاري التحقق...');
                $('#ajax-error').hide();

                // جلب التوكن مباشرة من سيشن لارافل وطباعته داخل الجافاسكربت
                let apiToken = "{{ session('token') }}";

                $.ajax({
                    url: `https://tztfn-196-1-227-87.run.pinggy-free.link/tma/customer/name/${customerNum}`,
                    method: "GET",
                    headers: {
                        // إرسال التوكن في الهيدر كـ Bearer Token المطلوبة للـ API
                        "Authorization": "Bearer " + apiToken,
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                        "Accept": "application/json"
                    },
                    success: function(response) {
                        if (response.success && response.name) {
                            // تعبئة حقل الاسم الراجع من الـ API
                            $('#customer_name').val(response.name);

                            // تثبيت رقم الحساب في خانة "إلى الحساب"
                            $('#to_account').html(
                                `<option value="${customerNum}">${customerNum}</option>`);

                            // فتح وتفعيل بقية الحقول والبيانات للتحويل
                            $('#remaining-fields').removeClass('disabled-group');

                            // إرجاع الزر لوضعه الطبيعي
                            btn.prop('disabled', false).text('التحقق');
                        } else {
                            $('#ajax-error').text("فشل التحقق، العميل غير موجود").show();
                            btn.prop('disabled', false).text('التحقق');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            $('#ajax-error').text(
                                "انتهت صلاحية الجلسة أو التوكن غير صحيح، يرجى إعادة تسجيل الدخول"
                                ).show();
                        } else {
                            $('#ajax-error').text(
                                    "رقم الحساب غير صحيح أو تعذر الاتصال بالسيرفر الرئيسي")
                                .show();
                        }
                        btn.prop('disabled', false).text('التحقق');
                    }
                });
            });

            // قفل الزر النهائي عند الضغط لمنع التكرار
            $('#transfer-form').submit(function() {
                let submitBtn = $('#submit-transfer-btn');
                submitBtn.prop('disabled', true).text('جاري إرسال طلب التحويل...');
            });
        });
    </script>
@endsection
