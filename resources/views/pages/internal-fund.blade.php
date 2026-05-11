@extends('layouts.app')
@section('title', 'تحويل داخلي')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn"><i class="fas fa-arrow-right"></i></a>
    <h2>تحويل داخلي</h2>
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
        <h3><i class="fas fa-exchange-alt"></i> تحويل داخلي</h3>
        <form method="POST" action="#">
            @csrf
            <div class="form-group">
                <label for="from_account">من حساب</label>
                <select id="from_account" name="from_account" class="form-control" required>
                    <option value="0210 0278242 00131 01000">0210 0278242 00131 01000 - ادخار</option>
                    <option value="0210 0278242 00132 01000">0210 0278242 00132 01000 - جاري</option>
                </select>
            </div>
            <div class="form-group">
                <label for="to_account">إلى حساب</label>
                <select id="to_account" name="to_account" class="form-control" required>
                    <option value="0210 0278242 00132 01000">0210 0278242 00132 01000 - جاري</option>
                    <option value="0210 0278242 00131 01000">0210 0278242 00131 01000 - ادخار</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">المبلغ</label>
                <input type="number" id="amount" name="amount" class="form-control" placeholder="أدخل المبلغ" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> تحويل</button>
        </form>
    </div>
</div>
@endsection
