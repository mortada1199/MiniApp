@extends('layouts.app')
@section('title', 'تحويل داخلي')

@section('content')

    <div class="page-header">

        <a href="{{ route('dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-right"></i>
        </a>

        <h2>تحويل داخلي</h2>

        <div style="width:36px"></div>

    </div>

    <div class="page-content fade-in">

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())

            <div class="alert alert-error">

                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach

            </div>

        @endif

        <div class="info-card">
            <h3>
                <i class="fas fa-exchange-alt"></i>
                تحويل داخلي
            </h3>
            <form method="POST" action="{{ route('internal-fund.submit') }}">

                @csrf

                <!-- From Account -->

                <div class="form-group">

                    <label for="accountFrom">
                        من حساب
                    </label>

                    <select id="accountFrom" name="accountFrom" class="form-control" required>

                        @foreach (session('accounts') as $account)
                            <option value="{{ $account['accountNo'] }}">

                                {{ $account['accountNo'] }}
                                -
                                {{ $account['ledModelDesc'] }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- To Account -->

                <div class="form-group">

                    <label for="accountTo">
                        إلى حساب
                    </label>

                    <select id="accountTo" name="accountTo" class="form-control" required>

                        @foreach (session('accounts') as $account)
                            <option value="{{ $account['accountNo'] }}">

                                {{ $account['accountNo'] }}
                                -
                                {{ $account['ledModelDesc'] }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- Amount -->

                <div class="form-group">

                    <label for="amount">
                        المبلغ
                    </label>

                    <input type="number" id="amount" name="amount" class="form-control" placeholder="أدخل المبلغ"
                        min="1" required>

                </div>

                <button type="submit" class="btn btn-primary">

                    <i class="fas fa-exchange-alt"></i>

                    تحويل

                </button>

            </form>

        </div>

    </div>

@endsection
