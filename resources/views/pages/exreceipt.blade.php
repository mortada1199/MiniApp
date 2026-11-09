@extends('layouts.app')
@section('title', 'ملخص الإشعار')

@section('content')
    @include('pages._receipt', [
        'amount' => $transaction['amount'],
        'rows' => [
            ['رقم الحركة', $transaction['reference_no'] ?? null, true],
            ['تاريخ الحركة', $transaction['date'], true],
            ['نوع الحركة', $transaction['type'], false],
            ['من حساب', $transaction['from_account'], true],
            ['إلى حساب', $transaction['to_account'], true],
            ['اسم المستفيد', $transaction['client_name'], false],
            ['رقم الهاتف المحمول', $transaction['phone'] ?? null, true],
            ['تعليقات', $transaction['notes'] ?? null, false],
        ],
    ])
@endsection
