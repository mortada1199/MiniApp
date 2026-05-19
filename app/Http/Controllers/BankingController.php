<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class BankingController extends Controller
{
    public function intFund()
    {
        $allAccounts = session('accounts', []);
        $filteredAccounts = array_filter($allAccounts, function ($account) {
            $accountNo = trim((string) ($account['accountNo'] ?? ''));

            return strlen($accountNo) >= 14 && $accountNo[13] === '1';
        });
        $accounts = collect($filteredAccounts)->values()->all();

        return view('pages.internal-fund', compact('accounts'));
    }

    public function submitIntFund(Request $request)
    {

        $type = 'تحويل داخلي ';
        $request->validate([
            'accountFrom' => 'required',
            'accountTo' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);
        $token = Session::get('token');
        $response = Http::withToken($token)
            ->post(
                'https://sokoq-196-1-227-87.run.pinggy-free.link/tma/transfer',
                [
                    'accountFrom' => $request->accountFrom,
                    'accountTo' => $request->accountTo,
                    'amount' => $request->amount,
                    'remarkCredit' => 'Salary May',
                    'remarkDebit' => 'Transfer to checking',
                ]
            );
        if ($response->successful()) {
            return view('pages.receipt', compact('request', 'type'));
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

    public function extFund()
    {
        $allAccounts = session('accounts', []);
        $filteredAccounts = array_filter($allAccounts, function ($account) {
            $accountNo = trim((string) ($account['accountNo'] ?? ''));

            return strlen($accountNo) >= 14 && $accountNo[13] === '1';
        });
        $accounts = collect($filteredAccounts)->values()->all();

        return view('pages.external-fund', compact('accounts'));
    }

    public function submiTExFund(Request $request)
    {
        
        $request->validate([
            'from_account' => 'required',
            'bban' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);
        $token = Session::get('token');
        $response = Http::withToken($token)
            ->acceptJson()
            ->post('https://sokoq-196-1-227-87.run.pinggy-free.link/tma/transfer', [
                'accountFrom' => $request->from_account,
                'accountTo' => $request->bban,
                'amount' => $request->amount,
                'remarkCredit' => 'Transfer to customer',
                'remarkDebit' => 'Transfer to customer',
            ]);

        if ($response->successful()) {
            $type = 'التحويل الى عميل داخل البنك';
            $result = $response->json();
            $transaction = [
                'reference_no' => $result['traceId'] ?? '260516'.rand(100000, 999999).'895555',
                'date' => now()->format('d/m/Y h:i:s A'),
                'type' => $type,
                'from_account' => $request->from_account,
                'to_account' => $request->bban,
                'client_name' => $request->customer_name,
                'phone' => $request->phone ?? '-----',
                'notes' => $request->note ?? '-----',
                'amount' => $request->amount,
            ];

            return view('pages.exreceipt', compact('transaction'));
        }

        $errorMessage = $response->json()['message'] ?? 'حدث خطأ في عملية التحويل، يرجى المحاولة لاحقاً';

        return back()->with('error', $errorMessage)->withInput();
    }


    public function verifyCustomerProxy($customerNum)
    {
        // 1. سحب التوكن من السيشن بأمان
        $apiToken = session('token');

        // 2. رابط السيرفر (تأكد أن tztfn هو الرابط الشغال حالياً في جهاز الطرف الآخر)
        $apiUrl = "https://sokoq-196-1-227-87.run.pinggy-free.link/tma/customer/name/{$customerNum}";

        try {
            // 3. تنفيذ الطلب مع تخطي حظر الشهادات
            $response = Http::withToken($apiToken)
                ->withoutVerifying()
                ->acceptJson()
                ->timeout(10) // وضع حد أقصى للانتظار 10 ثوانٍ حتى لا يعلق السيرفر
                ->get($apiUrl);

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json([
                'success' => false,
                'message' => 'العميل غير موجود في قواعد بيانات البنك',
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('فشل اتصال الـ Proxy بنفق البنك: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'تعذر الاتصال بخادم البنك الخارجي (يرجى التأكد من تشغيل نفق Pinggy)',
                'debug_error' => $e->getMessage(), // سيظهر لك سبب الفشل الفعلي في المتصفح أثناء التطوير
            ], 500);
        }
    }

    public function verifyBban(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => 'murtada',
                'bank' => 'ONB',
                'limits' => '10.00 SDG - 500,000.00 SDG', // أو حسب برمجتك
            ],
        ]);

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
