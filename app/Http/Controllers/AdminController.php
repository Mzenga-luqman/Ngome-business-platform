<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\NewSubscriptionPaymentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function payments(Request $request): View
    {
        $pendingPayments = Payment::with(['user', 'subscription', 'invoice'])
            ->where('status', Payment::STATUS_PENDING)
            ->latest('id')
            ->get();

        $approvedPayments = Payment::with(['user', 'subscription', 'invoice', 'reviewer'])
            ->where('status', Payment::STATUS_APPROVED)
            ->orderByDesc('reviewed_at')
            ->take(100)
            ->get();

        $pendingPasswordResetRequests = PasswordResetRequest::query()
            ->with('user')
            ->where('status', PasswordResetRequest::STATUS_PENDING)
            ->latest('requested_at')
            ->get();

        $recentPasswordResetRequests = PasswordResetRequest::query()
            ->with(['user', 'processor'])
            ->where('status', PasswordResetRequest::STATUS_COMPLETED)
            ->latest('processed_at')
            ->take(50)
            ->get();

        $users = User::query()
            ->with('subscription')
            ->orderByDesc('created_at')
            ->get();

        $admin = $request->user();
        $recentNotifications = $admin->notifications()
            ->where('type', NewSubscriptionPaymentNotification::class)
            ->latest()
            ->take(10)
            ->get();

        $unreadAlerts = $admin->unreadNotifications()
            ->where('type', NewSubscriptionPaymentNotification::class)
            ->count();

        $admin->unreadNotifications()
            ->where('type', NewSubscriptionPaymentNotification::class)
            ->update(['read_at' => now()]);

        return view('admin.payments', [
            'pendingPayments' => $pendingPayments,
            'approvedPayments' => $approvedPayments,
            'users' => $users,
            'recentNotifications' => $recentNotifications,
            'unreadAlerts' => $unreadAlerts,
            'pendingPasswordResetRequests' => $pendingPasswordResetRequests,
            'recentPasswordResetRequests' => $recentPasswordResetRequests,
        ]);
    }

    public function resetUserPassword(Request $request, PasswordResetRequest $passwordResetRequest): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($passwordResetRequest->status !== PasswordResetRequest::STATUS_PENDING) {
            return back()->with('error', 'This password reset request has already been processed.');
        }

        DB::transaction(function () use ($passwordResetRequest, $request, $validated) {
            $passwordResetRequest->user->forceFill([
                'password' => Hash::make($validated['new_password']),
                'remember_token' => Str::random(60),
            ])->save();

            $passwordResetRequest->update([
                'status' => PasswordResetRequest::STATUS_COMPLETED,
                'processed_by' => $request->user()->id,
                'processed_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'User password has been reset successfully.');
    }

    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'This payment has already been reviewed.');
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->loadMissing(['user', 'subscription', 'invoice']);

            $user = $payment->user;
            $subscription = $payment->subscription;

            $baseDate = $user->subscription_expiry && $user->subscription_expiry->isFuture()
                ? $user->subscription_expiry->copy()
                : now();

            // Admin accounts never expire; set to year 9999
            $expiryDate = $user->is_admin
                ? now()->year(9999)->endOfYear()
                : $baseDate->addMonths($subscription->duration_months);

            $user->update([
                'subscription_id' => $subscription->id,
                'subscription_expiry' => $expiryDate,
            ]);

            $payment->update([
                'status' => Payment::STATUS_APPROVED,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $payment->invoice->update([
                'status' => Invoice::STATUS_APPROVED,
            ]);
        });

        return back()->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'This payment has already been reviewed.');
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $payment->invoice()->update([
                'status' => Invoice::STATUS_REJECTED,
            ]);
        });

        return back()->with('success', 'Payment rejected.');
    }
}
