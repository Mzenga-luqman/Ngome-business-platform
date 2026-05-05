<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NewSubscriptionPaymentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(Request $request, Subscription $subscription): View
    {
        $user = $request->user()->subscriptionAccount();

        return view('subscription.payment', [
            'subscription' => $subscription,
            'user' => $user,
            'paymentMethods' => $this->getPaymentMethods(),
        ]);
    }

    public function store(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:airtel,tigo,vodacom,mpesa'],
            'phone_number' => ['required', 'string', 'min:8', 'max:30'],
            'payment_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);

        $user = $request->user()->subscriptionAccount();

        $result = DB::transaction(function () use ($validated, $subscription, $user) {
            $invoiceCode = $this->generateInvoiceCode($user->id);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'invoice_code' => $invoiceCode,
                'amount' => $subscription->price,
                'status' => Invoice::STATUS_PAID,
            ]);

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'provider' => strtolower($validated['provider']),
                'phone_number' => $validated['phone_number'],
                'payment_reference' => strtoupper(trim($validated['payment_reference'])),
                'status' => Payment::STATUS_PENDING,
            ]);

            return ['invoice' => $invoice, 'payment' => $payment];
        });

        $admins = User::query()->where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewSubscriptionPaymentNotification(
                $result['payment']->loadMissing(['user', 'subscription', 'invoice'])
            ));
        }

        return redirect()
            ->route('subscription.pending')
            ->with('success', 'Payment submitted! Waiting for admin approval.');
    }

    private function generateInvoiceCode(int $userId): string
    {
        do {
            $code = 'NGOME-' . $userId . '-' . Str::upper(Str::random(8));
        } while (Invoice::where('invoice_code', $code)->exists());

        return $code;
    }

    private function getPaymentMethods(): array
    {
        return [
            [
                'key' => 'airtel',
                'name' => 'Airtel Money',
                'code' => '*150*60#',
                'number' => '0694212898',
                'ussd_link' => 'tel:*150*60%23',
                'instructions' => 'Tap the button and Airtel USSD opens instantly on your phone.',
                'color' => 'from-red-500/20 to-red-700/10 border-red-400/30',
                'icon' => '📱',
            ],
            [
                'key' => 'tigo',
                'name' => 'Tigo Money',
                'code' => '*150*01#',
                'number' => '0658018393',
                'ussd_link' => 'tel:*150*01%23',
                'instructions' => 'Tap once and Tigo USSD opens so the customer can pay immediately.',
                'color' => 'from-blue-500/20 to-blue-700/10 border-blue-400/30',
                'icon' => '📞',
            ],
            [
                'key' => 'vodacom',
                'name' => 'Vodacom Cash',
                'code' => '*150*00#',
                'number' => '0694212898',
                'ussd_link' => 'tel:*150*00%23',
                'instructions' => 'Use Vodacom Cash and send the exact plan amount to the copied number.',
                'color' => 'from-rose-500/20 to-rose-700/10 border-rose-400/30',
                'icon' => '📶',
            ],
            [
                'key' => 'mpesa',
                'name' => 'M-Pesa',
                'code' => '*150*00#',
                'number' => '0694212898',
                'ussd_link' => 'tel:*150*00%23',
                'instructions' => 'Open M-Pesa instantly, pay, then come back and tap Paid below.',
                'color' => 'from-green-500/20 to-green-700/10 border-green-400/30',
                'icon' => '💳',
            ],
        ];
    }
}

