<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subscription Plans</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes drift {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .live-card { animation: drift 4s ease-in-out infinite; }
        .live-card:nth-child(2) { animation-delay: .35s; }
        .live-card:nth-child(3) { animation-delay: .7s; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 text-white">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-4xl font-black">Live Subscription Plans</h1>
                <p class="text-sm text-blue-100/70">Choose a plan to continue using Ngome POS.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('subscription.status') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">Payment Status</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.payments') }}" class="rounded-xl bg-white px-4 py-2 text-sm font-black text-slate-900 hover:bg-slate-100">Admin Payments</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">Logout</button>
                </form>
            </div>
        </div>

        @if(session('warning'))
            <div class="mb-6 rounded-2xl border border-amber-300/40 bg-amber-500/10 px-5 py-4 text-amber-100">
                <p class="font-bold">Subscription expired</p>
                <p class="mt-1 text-sm">{{ session('warning') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-300/40 bg-red-500/10 px-5 py-4 text-red-100">{{ session('error') }}</div>
        @endif

        @if($user->hasActiveSubscription())
            <div class="mb-6 rounded-2xl border border-emerald-300/40 bg-emerald-500/10 px-5 py-4 text-emerald-100">
                <p class="font-bold">Active subscription</p>
                <p class="mt-1 text-sm">
                    {{ $user->subscription?->name }} plan active until {{ $user->subscription_expiry?->format('d M Y, h:i A') }}
                    ({{ $user->remainingSubscriptionDays() }} day(s) left).
                </p>
                <a href="{{ route('dashboard') }}" class="mt-3 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-black text-slate-900 hover:bg-slate-100">Go to Dashboard</a>
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            @forelse($plans as $plan)
                <div class="live-card rounded-2xl border border-white/15 bg-white/10 p-5 shadow-sm backdrop-blur hover:-translate-y-1 transition duration-300">
                    <p class="text-xs font-bold uppercase tracking-wide text-blue-200">{{ $plan->name }}</p>
                    <p class="mt-2 text-3xl font-black text-white">TZS {{ number_format($plan->price, 0) }}</p>
                    <p class="mt-1 text-sm text-white/70">Valid for {{ $plan->duration_months }} month(s)</p>

                    <a href="{{ route('invoice.show', $plan) }}" class="mt-5 inline-block w-full rounded-xl bg-white px-4 py-3 text-center text-sm font-black text-slate-900 hover:bg-slate-100 transition">
                        Choose Plan
                    </a>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-white/15 bg-white/10 px-5 py-10 text-center text-white/70">
                    No plans configured yet. Please contact admin.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
