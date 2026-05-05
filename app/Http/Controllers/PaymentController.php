<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, string $invoiceCode): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'min:8', 'max:30'],
            'payment_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);

        $user = $request->user();

        $invoice = Invoice::where('invoice_code', $invoiceCode)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (! in_array($invoice->status, [Invoice::STATUS_PENDING, Invoice::STATUS_PAID], true)) {
            return back()->with('error', 'This invoice is already closed.');
        }

        DB::transaction(function () use ($validated, $invoice, $user) {
            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $invoice->subscription_id,
                'invoice_id' => $invoice->id,
                'phone_number' => $validated['phone_number'],
                'payment_reference' => strtoupper(trim($validated['payment_reference'])),
                'status' => Payment::STATUS_PENDING,
            ]);

            $invoice->update([
                'status' => Invoice::STATUS_PAID,
            ]);
        });

        return redirect()
            ->route('subscription.status')
            ->with('success', 'Payment submitted. Waiting for admin confirmation.');
    }
}
