<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subscription Status</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="mb-6 flex items-center justify-between gap-3">
            <a href="{{ route('subscription.plans') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Plans</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-black">Subscription Status</h1>

            @if($user->hasActiveSubscription())
                <div class="mt-4 rounded-xl border border-emerald-300 bg-emerald-50 p-4">
                    <p class="text-xl font-black text-emerald-700">Activated 🎉</p>
                    <p class="mt-2 text-sm text-emerald-900">Plan: {{ $user->subscription?->name }}</p>
                    <p class="text-sm text-emerald-900">Expires on: {{ $user->subscription_expiry?->format('d M Y, h:i A') }}</p>
                    <p class="text-sm text-emerald-900">Remaining: {{ $user->remainingSubscriptionDays() }} day(s)</p>
                </div>
                <a href="{{ route('dashboard') }}" class="mt-5 inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white hover:bg-blue-700">Go to Dashboard</a>
            @else
                <div class="mt-4 rounded-xl border border-amber-300 bg-amber-50 p-4">
                    <p class="text-xl font-black text-amber-700">Waiting for confirmation ⏳</p>
                    <p class="mt-2 text-sm text-amber-900">Your payment is pending admin review.</p>
                </div>
                <a href="{{ route('subscription.plans') }}" class="mt-5 inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white hover:bg-blue-700">Back to Plans</a>
            @endif
        </div>
    </div>
</body>
</html>
