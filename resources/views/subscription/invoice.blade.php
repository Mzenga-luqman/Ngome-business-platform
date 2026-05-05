<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Invoice</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6">
        <a href="{{ route('subscription.plans') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Plans</a>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-black">Payment Invoice</h1>
            <p class="mt-1 text-sm text-slate-500">Send money first, then submit your payment details below.</p>

            @if(session('error'))
                <div class="mt-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="mt-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold text-slate-500">Plan</p>
                    <p class="mt-1 font-bold">{{ $invoice->subscription->name }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold text-slate-500">Amount</p>
                    <p class="mt-1 font-bold">TZS {{ number_format($invoice->amount, 0) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold text-slate-500">Status</p>
                    <p class="mt-1 font-bold capitalize">{{ $invoice->status }}</p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border-2 border-blue-200 bg-blue-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-blue-600">Invoice Code</p>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p id="invoice-code" class="text-lg font-black tracking-wide text-blue-900">{{ $invoice->invoice_code }}</p>
                    <button type="button" onclick="copyInvoiceCode()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">Copy</button>
                </div>
                <p class="mt-2 text-xs text-blue-800">Send money and include this code in the message.</p>
            </div>

            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                <p class="font-bold">Mobile Money Numbers</p>
                <ul class="mt-2 space-y-1">
                    <li>M-Pesa: <span class="font-black">+255 700 111 222</span></li>
                    <li>Mixx: <span class="font-black">+255 700 333 444</span></li>
                    <li>Airtel Money: <span class="font-black">+255 700 555 666</span></li>
                </ul>
            </div>

            <form method="POST" action="{{ route('payment.store', ['invoiceCode' => $invoice->invoice_code]) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                           placeholder="e.g. 07XXXXXXXX">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Transaction ID</label>
                    <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                           placeholder="Enter transaction reference">
                </div>

                <button type="submit" class="w-full rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white hover:bg-emerald-700">
                    I Have Paid
                </button>
            </form>
        </div>
    </div>

    <script>
        function copyInvoiceCode() {
            const code = document.getElementById('invoice-code')?.innerText?.trim() || '';
            if (!code) return;
            navigator.clipboard.writeText(code);
        }
    </script>
</body>
</html>
