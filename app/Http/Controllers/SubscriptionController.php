<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function plans(Request $request): View
    {
        $plans = Subscription::orderBy('price')->get();
        $user = $request->user()->subscriptionAccount();

        return view('subscription.plans', [
            'plans' => $plans,
            'user' => $user,
        ]);
    }

    public function status(Request $request): View
    {
        $user = $request->user()->subscriptionAccount();

        return view('subscription.status', [
            'user' => $user,
        ]);
    }

    public function pending(Request $request): View
    {
        $user = $request->user()->subscriptionAccount();
        $invoice = $user->invoices()->latest()->first();

        return view('subscription.pending', [
            'user' => $user,
            'invoice' => $invoice,
        ]);
    }
}
