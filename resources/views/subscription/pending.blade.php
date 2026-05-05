<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Pending - Ngome Shop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 0 rgba(59, 130, 246, 0.2); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6), 0 0 0 8px rgba(59, 130, 246, 0.1); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .glow-box { animation: pulse-glow 2s ease-in-out infinite; }
        .float { animation: float 3s ease-in-out infinite; }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .spin { animation: spin 3s linear infinite; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 font-sans antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
    </div>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-2xl">
            {{-- LOADING ANIMATION --}}
            <div class="mb-8 flex justify-center">
                <div class="relative h-20 w-20">
                    <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-400 border-r-blue-400 spin"></div>
                    <div class="absolute inset-2 rounded-full border-4 border-transparent border-b-emerald-400 border-l-emerald-400 spin" style="animation-direction: reverse; animation-duration: 4s;"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-2xl">⏳</div>
                </div>
            </div>

            {{-- MAIN MESSAGE --}}
            <div class="text-center mb-8 fade-in">
                <h1 class="text-4xl sm:text-5xl font-black text-white mb-3">
                    Payment Submitted!
                </h1>
                <p class="text-lg text-blue-200">
                    Your payment is awaiting admin verification
                </p>
            </div>

            {{-- PAYMENT DETAILS CARD --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-6 sm:p-8 mb-8 glow-box fade-in" style="animation-delay: 0.1s">
                <h2 class="text-sm font-black uppercase tracking-widest text-blue-300 mb-6">Payment Details</h2>

                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs text-white/60 font-semibold">Invoice Code</p>
                        <div class="flex items-center gap-2 mt-2">
                            <p class="text-xl font-black text-emerald-400 font-mono">{{ $invoice->invoice_code }}</p>
                            <button type="button" onclick="copyInvoiceCode('{{ $invoice->invoice_code }}')" class="text-white/60 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs text-white/60 font-semibold">Amount Paid</p>
                        <p class="text-2xl font-black text-white mt-2">TZS {{ number_format($invoice->amount, 0) }}</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs text-white/60 font-semibold">Plan</p>
                        <p class="text-lg font-bold text-blue-300 mt-2">{{ $invoice->subscription->name }}</p>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs text-white/60 font-semibold">Duration</p>
                        <p class="text-lg font-bold text-emerald-300 mt-2">{{ $invoice->subscription->duration_months }}
                            {{ $invoice->subscription->duration_months == 1 ? 'Month' : 'Months' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs text-white/60 font-semibold">Submitted At</p>
                    <p class="text-sm text-white/80 mt-2">{{ $invoice->created_at->format('M d, Y - H:i') }}</p>
                </div>
            </div>

            {{-- STATUS ALERT --}}
            <div class="rounded-2xl border-2 border-blue-400/50 bg-blue-500/10 p-6 mb-8 fade-in" style="animation-delay: 0.2s">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 text-2xl">⏱️</div>
                    <div>
                        <h3 class="text-lg font-black text-blue-300">Under Review</h3>
                        <p class="text-sm text-blue-200/80 mt-1.5">
                            Our admin dashboard has already received your payment alert. Verification usually takes less than 30 minutes, then your subscription activates immediately.
                        </p>
                        <p class="text-xs text-blue-200/60 mt-3">
                            Keep this invoice code handy: <span class="font-mono font-bold text-blue-300">{{ $invoice->invoice_code }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ACTION CARDS --}}
            <div class="grid sm:grid-cols-2 gap-4 mb-8 fade-in" style="animation-delay: 0.3s">
                <a href="{{ route('subscription.status') }}" class="group rounded-xl border border-white/10 bg-white/5 backdrop-blur p-4 hover:bg-white/10 transition">
                    <div class="flex items-center gap-3">
                        <div class="text-2xl">📊</div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-blue-300 transition">Check Status</p>
                            <p class="text-xs text-white/60">View your subscription status</p>
                        </div>
                        <svg class="w-5 h-5 text-white/40 group-hover:text-blue-300 transition ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('login') }}" class="group rounded-xl border border-white/10 bg-white/5 backdrop-blur p-4 hover:bg-white/10 transition">
                    <div class="flex items-center gap-3">
                        <div class="text-2xl">🏠</div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-blue-300 transition">Refresh Login</p>
                            <p class="text-xs text-white/60">Return to login page</p>
                        </div>
                        <svg class="w-5 h-5 text-white/40 group-hover:text-blue-300 transition ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>

            {{-- NEXT STEPS --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-6 fade-in" style="animation-delay: 0.4s">
                <h3 class="text-sm font-black uppercase tracking-widest text-blue-300 mb-4">What Happens Next</h3>
                
                <div class="space-y-3">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-500/30 text-sm font-bold text-blue-300">1</div>
                        <div>
                            <p class="text-sm font-semibold text-white">Admin Verification</p>
                            <p class="text-xs text-white/60">Our team verifies your payment reference</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-500/30 text-sm font-bold text-blue-300">2</div>
                        <div>
                            <p class="text-sm font-semibold text-white">Approval Confirmation</p>
                            <p class="text-xs text-white/60">Your account is updated as soon as the admin approves</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-emerald-500/30 text-sm font-bold text-emerald-300">3</div>
                        <div>
                            <p class="text-sm font-semibold text-white">Access Granted</p>
                            <p class="text-xs text-white/60">Start using your Ngome POS dashboard</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AUTO REFRESH HINT --}}
            <div class="mt-8 text-center text-xs text-white/50">
                <p>This page auto-refreshes every 30 seconds...</p>
            </div>
        </div>
    </div>

    <script>
        function copyInvoiceCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Invoice code copied: ' + code);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }

        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
