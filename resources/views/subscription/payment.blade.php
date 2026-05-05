<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pay for {{ $subscription->name }} - Ngome Shop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-card { animation: floatCard 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 font-sans antialiased text-white">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-emerald-500/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto min-h-screen max-w-6xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('subscription.plans') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-semibold text-blue-300 hover:text-blue-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to plans
                </a>
                <h1 class="text-4xl font-black sm:text-5xl">Pay once. Activate fast.</h1>
            </div>
            <div class="floating-card rounded-3xl border border-white/10 bg-white/5 px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Plan</p>
                <p class="mt-1 text-2xl font-black">{{ $subscription->name }}</p>
                <p class="mt-1 text-sm text-white/70">TZS {{ number_format($subscription->price, 0) }} • {{ $subscription->duration_months }} months</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-300">Copy This Number</p>
                <div class="mt-2 flex items-center gap-3">
                    <p id="merchant-number" class="text-3xl font-black">0694212898</p>
                    <button type="button" onclick="copyMerchantNumber()" class="rounded-xl bg-white px-3 py-2 text-xs font-black text-slate-900">Copy</button>
                </div>

                <h2 class="mt-6 text-xl font-black">Choose provider (auto-dial)</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($paymentMethods as $method)
                        <button type="button" class="provider-card rounded-2xl border border-white/15 bg-white/10 p-4 text-left hover:bg-white/15"
                                onclick="selectProvider('{{ $method['key'] }}')" data-provider="{{ $method['key'] }}">
                            <p class="font-black">{{ $method['icon'] }} {{ $method['name'] }}</p>
                            <p class="text-sm text-white/70 mt-1">USSD: {{ $method['code'] }}</p>
                        </button>
                    @endforeach
                </div>

                <a id="dial-now-link" href="#" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-black text-white opacity-50 pointer-events-none">
                    Select provider first
                </a>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Selected Provider</p>
                <p id="selected-provider-name" class="mt-2 text-2xl font-black">None</p>
                <p id="selected-provider-code" class="text-sm text-white/70 mt-1">USSD: --</p>

                <form method="POST" action="{{ route('payment.store', ['subscription' => $subscription->id]) }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="provider" id="provider-input" value="{{ old('provider') }}">

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-400/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">{{ $errors->first() }}</div>
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/80">Your phone number</label>
                        <input type="text" name="phone_number" required value="{{ old('phone_number') }}" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white" placeholder="0712345678">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/80">Transaction Reference</label>
                        <input type="text" name="payment_reference" required value="{{ old('payment_reference') }}" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white" placeholder="Reference from SMS">
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-white px-5 py-3.5 text-sm font-black uppercase tracking-wide text-slate-950">Paid</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        const paymentMethods = @json($paymentMethods);
        const providerInput = document.getElementById('provider-input');
        const selectedProviderName = document.getElementById('selected-provider-name');
        const selectedProviderCode = document.getElementById('selected-provider-code');
        const merchantNumber = document.getElementById('merchant-number');
        const dialNowLink = document.getElementById('dial-now-link');

        function copyMerchantNumber() {
            navigator.clipboard.writeText(merchantNumber.textContent.trim()).then(() => alert('Number copied'));
        }

        function selectProvider(providerKey) {
            const method = paymentMethods.find((item) => item.key === providerKey);
            if (!method) return;

            providerInput.value = method.key;
            selectedProviderName.textContent = method.name;
            selectedProviderCode.textContent = `USSD: ${method.code}`;
            merchantNumber.textContent = method.number;

            dialNowLink.href = method.ussd_link;
            dialNowLink.textContent = `Dial ${method.code} now`;
            dialNowLink.classList.remove('opacity-50', 'pointer-events-none');

            document.querySelectorAll('.provider-card').forEach((card) => card.classList.remove('ring-2', 'ring-emerald-400'));
            const active = document.querySelector(`[data-provider="${providerKey}"]`);
            if (active) active.classList.add('ring-2', 'ring-emerald-400');

            window.location.href = method.ussd_link;
        }

        if (providerInput.value) selectProvider(providerInput.value);
    </script>
</body>
</html>
