@extends('layouts.app')
@section('title', 'ملخص الإشعار')

@section('content')
    @include('pages._receipt', [
        'amount' => $request['amount'],
        'rows' => [
            ['تاريخ الحركة', now()->format('d/m/Y h:i:s A'), true],
            ['نوع الحركة', $type, false],
            ['من حساب', $request['accountFrom'], true],
            ['إلى حساب', $request['accountTo'], true],
            ['اسم العميل', data_get(session('accounts'), '0.cusShoName'), false],
            ['تعليقات', $request['note'], false],
        ],
    ])
@endsection
