@extends('layouts.app')
@section('title', 'تحويل خارجي')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
    <h2>تحويل خارجي</h2>
    <div style="width:36px"></div>
</div>

<div class="page-content fade-in">
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="info-card">
        <h3><i class="fas fa-paper-plane"></i> تحويل خارجي</h3>
        <form method="POST" action="{{ route('external-fund.submit') }}">
            @csrf
            <div class="form-group">
                <label for="from_account">من حساب</label>
                <select id="from_account" name="from_account" class="form-control">
                    <option value="0210 0278242 00131 01000">0210 0278242 00131 01000 - ادخار</option>
                    <option value="0210 0278242 00132 01000">0210 0278242 00132 01000 - جاري</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bank">البنك المستفيد</label>
                <select id="bank" name="bank" class="form-control" required>
                    <option value="">اختر البنك</option>
                    <option value="khartoum">بنك الخرطوم</option>
                    <option value="omdurman">بنك أم درمان</option>
                    <option value="ahli">البنك الأهلي</option>
                    <option value="faisal">بنك فيصل الإسلامي</option>
                </select>
            </div>
            <div class="form-group">
                <label for="account">رقم حساب المستفيد</label>
                <input type="text" id="account" name="account" class="form-control" placeholder="أدخل رقم الحساب" required>
            </div>
            <div class="form-group">
                <label for="amount">المبلغ</label>
                <input type="number" id="amount" name="amount" class="form-control" placeholder="أدخل المبلغ" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> تحويل</button>
        </form>
    </div>
</div>
@endsection
