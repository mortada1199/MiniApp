<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class BankingController extends Controller
{
    public function intFund()
    {
        $accounts = Session::get('accounts', []);
        $accounts = array_filter($accounts, function ($account) {
            $accountNo = (string) $account['accountNo'];
            return isset($accountNo[13]) && $accountNo[13] === '1';
        });

        return view('pages.internal-fund', compact('accounts'));
    }

    public function submitIntFund(Request $request)
    {
        $request->validate([
            'accountFrom' => 'required',
            'accountTo' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);
        $token = Session::get('token');
        $response = Http::withToken($token)
            ->post(
                'https://rgdrs-196-1-227-87.run.pinggy-free.link/tma/transfer',
                [
                    'accountFrom' => $request->accountFrom,
                    'accountTo' => $request->accountTo,
                    'amount' => $request->amount,
                    'remarkCredit' => 'Salary May',
                    'remarkDebit' => 'Transfer to checking',
                ]
            );
        // dd($response->status(), $response->body());

        if ($response->successful()) {
            return back()->with(
                'success',
                'تم التحويل الداخلي بنجاح'
            );
        }

        return back()->with(
            'error',
            'حدث خطأ في عملية التحويل'
        );
    }

    public function accList()
    {
        return view('pages.accounts');
    }

    public function verifyBban(Request $request)
    {
        // ابحث عن الحساب في قاعدة البيانات
        //  $account = Account::where('bban_number', $request->bban)->first();

        // if ($account) {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => 'murtada',
                'bank' => 'ONB',
                'limits' => '10.00 SDG - 500,000.00 SDG', // أو حسب برمجتك
            ],
        ]);
        // }

        // return response()->json(['success' => false], 404);
    }

    public function tranToBBAN()
    {
        return view('pages.transfer-bban');
    }

    // public function changePin()
    // {
    //     return view('pages.change-pin');
    // }

    // public function submitChangePin(Request $request)
    // {
    //     $request->validate([
    //         'current_pin' => 'required|digits:4',
    //         'new_pin' => 'required|digits:4',
    //         'confirm_pin' => 'required|same:new_pin',
    //     ]);
    //     return back()->with('success', 'تم تغيير الرقم السري بنجاح');
    // }

    // public function accTrans()
    // {
    //     $transactions = [
    //         ['date' => '2026-05-10', 'description' => 'تحويل صادر', 'amount' => '-5,000', 'balance' => '45,000'],
    //         ['date' => '2026-05-09', 'description' => 'إيداع نقدي', 'amount' => '+10,000', 'balance' => '50,000'],
    //         ['date' => '2026-05-08', 'description' => 'دفع فاتورة كهرباء', 'amount' => '-1,200', 'balance' => '40,000'],
    //         ['date' => '2026-05-07', 'description' => 'تحويل وارد', 'amount' => '+15,000', 'balance' => '41,200'],
    //         ['date' => '2026-05-06', 'description' => 'سحب ATM', 'amount' => '-3,000', 'balance' => '26,200'],
    //     ];
    //     return view('pages.transactions', compact('transactions'));
    // }

    // public function tranToBBAN()
    // {
    //     return view('pages.transfer-bban');
    // }

    // public function submitTransferBBAN(Request $request)
    // {
    //     $request->validate([
    //         'bban' => 'required',
    //         'amount' => 'required|numeric|min:1',
    //     ]);
    //     return back()->with('success', 'تمت عملية التحويل بنجاح');
    // }

    // public function extFund()
    // {
    //     return view('pages.external-fund');
    // }

    // public function submitExtFund(Request $request)
    // {
    //     $request->validate([
    //         'bank' => 'required',
    //         'account' => 'required',
    //         'amount' => 'required|numeric|min:1',
    //     ]);
    //     return back()->with('success', 'تم التحويل الخارجي بنجاح');
    // }

    public function payment()
    {
        return view('pages.payment');
    }

    public function submitPayment(Request $request)
    {
        $request->validate([
            'service' => 'required',
            'reference' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        return back()->with('success', 'تم دفع الفاتورة بنجاح');
    }
}
