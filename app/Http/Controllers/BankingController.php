<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankingController extends Controller
{
    private const TRANSFER_FAILED = 'حدث خطأ في عملية التحويل، يرجى المحاولة لاحقاً';

    private const CONNECTION_FAILED = 'تعذر الاتصال بخادم البنك، يرجى المحاولة لاحقاً';

    /**
     * حسابات المستخدم التي يسمح بالتحويل منها (الخانة رقم 14 في رقم الحساب = 1)
     */
    private function transferableAccounts(): array
    {
        return collect(session('accounts', []))
            ->filter(function ($account) {
                $accountNo = trim((string) ($account['accountNo'] ?? ''));

                return strlen($accountNo) >= 14 && $accountNo[13] === '1';
            })
            ->values()
            ->all();
    }

    /**
     * إرسال طلب تحويل لـ API البنك. يرجّع null لو فشل الاتصال (مهلة / الخادم غير متاح)
     */
    private function postTransfer(array $payload): ?Response
    {
        try {
            $response = $this->tmaApi(session('token'))->post('/transfer', $payload);
        } catch (ConnectionException $e) {
            Log::error('TMA transfer: connection failed', ['error' => $e->getMessage()]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('TMA transfer: request failed', [
                'status' => $response->status(),
                'message' => $response->json('message'),
            ]);
        }

        return $response;
    }

    public function intFund()
    {
        $accounts = $this->transferableAccounts();

        return view('pages.internal-fund', compact('accounts'));
    }

    public function submitIntFund(Request $request)
    {
        $type = 'تحويل داخلي';
        $request->validate([
            'accountFrom' => 'required',
            'accountTo' => 'required|different:accountFrom',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:100',
        ]);

        $remark = $request->filled('note') ? $request->note : $type;

        $response = $this->postTransfer([
            'accountFrom' => $request->accountFrom,
            'accountTo' => $request->accountTo,
            'amount' => $request->amount,
            'remarkCredit' => $remark,
            'remarkDebit' => $remark,
        ]);

        if ($response === null) {
            return back()->with('error', self::CONNECTION_FAILED)->withInput();
        }

        if ($response->successful()) {
            return view('pages.receipt', compact('request', 'type'));
        }

        return back()
            ->with('error', $response->json('message') ?? self::TRANSFER_FAILED)
            ->withInput();
    }

    public function accList()
    {
        return view('pages.accounts');
    }

    public function transferInternal()
    {
        $accounts = $this->transferableAccounts();

        return view('pages.transfer-internal', compact('accounts'));
    }

    public function submitTransferInternal(Request $request)
    {
        $request->validate([
            'from_account' => 'required',
            'to_account' => 'required|different:from_account',
            'amount' => 'required|numeric|min:1',
        ]);

        // accountTo = حساب المستفيد المختار بعد "التحقق" (وليس bban الذي هو رقم البحث فقط)
        $response = $this->postTransfer([
            'accountFrom' => $request->from_account,
            'accountTo' => $request->to_account,
            'amount' => $request->amount,
            'remarkCredit' => 'Transfer to customer',
            'remarkDebit' => 'Transfer to customer',
        ]);

        if ($response === null) {
            return back()->with('error', self::CONNECTION_FAILED)->withInput();
        }

        if ($response->successful()) {
            $type = 'التحويل الى عميل داخل البنك';
            $result = $response->json();
            $transaction = [
                'reference_no' => $result['traceId'] ?? '260516'.rand(100000, 999999).'895555',
                'date' => now()->format('d/m/Y h:i:s A'),
                'type' => $type,
                'from_account' => $request->from_account,
                'to_account' => $request->to_account,
                'client_name' => $request->customer_name,
                'phone' => $request->phone ?? '-----',
                'notes' => $request->note ?? '-----',
                'amount' => $request->amount,
            ];

            return view('pages.exreceipt', compact('transaction'));
        }

        return back()
            ->with('error', $response->json('message') ?? self::TRANSFER_FAILED)
            ->withInput();
    }


    public function verifyCustomerProxy($customerNum)
    {
        // 1. سحب التوكن من السيشن بأمان
        try {
            // 2. تنفيذ الطلب مع تخطي حظر الشهادات (الرابط والمهلة من config/services.php)
            $response = $this->tmaApi(session('token'))
                ->withoutVerifying()
                ->get('/customer/name/'.rawurlencode($customerNum));

            if ($response->successful()) {
                $data = $response->json();

                // تطبيع رد البنك: ابحث عن الاسم والحسابات تحت أي من الأسماء الشائعة
                $name = data_get($data, 'name')
                    ?? data_get($data, 'customerName')
                    ?? data_get($data, 'fullName')
                    ?? data_get($data, 'custName')
                    ?? data_get($data, 'data.name')
                    ?? data_get($data, 'customer.name')
                    ?? data_get($data, 'result.name');

                $rawAccounts = data_get($data, 'customerAccounts')
                    ?? data_get($data, 'accounts')
                    ?? data_get($data, 'accountList')
                    ?? data_get($data, 'data.customerAccounts')
                    ?? data_get($data, 'data.accounts')
                    ?? data_get($data, 'customer.accounts')
                    ?? [];

                $accounts = collect(is_array($rawAccounts) ? $rawAccounts : [])
                    ->map(fn ($a) => [
                        'accountNo' => data_get($a, 'accountNo')
                            ?? data_get($a, 'accountNumber')
                            ?? data_get($a, 'iban')
                            ?? data_get($a, 'acc'),
                        'ledModelDesc' => data_get($a, 'ledModelDesc')
                            ?? data_get($a, 'accountType')
                            ?? data_get($a, 'productDesc')
                            ?? data_get($a, 'desc')
                            ?? '',
                    ])
                    ->filter(fn ($a) => ! empty($a['accountNo']))
                    ->values()
                    ->all();

                return response()->json([
                    'success' => $name !== null,
                    'name' => $name,
                    'customerAccounts' => $accounts,
                    'raw' => $data, // الرد الأصلي من البنك (للتشخيص)
                ]);
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
