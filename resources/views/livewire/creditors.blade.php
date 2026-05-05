<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($successMessage)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 font-semibold">
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 font-semibold">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Creditors') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage unpaid credit sales and mark them paid when money is collected.') }}</p>
        </div>
        <div class="text-sm font-semibold text-slate-600">
            {{ __('Unpaid') }}: <span class="text-red-600">{{ $unpaidCreditors->count() }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-white rounded-2xl border border-slate-100 shadow p-4">
            <h2 class="text-lg font-bold text-slate-800 mb-3">{{ __('Unpaid Credits') }}</h2>

            @if($unpaidCreditors->isEmpty())
                <p class="text-sm text-slate-500">{{ __('No unpaid credit records.') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($unpaidCreditors as $creditor)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $creditor->customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $creditor->customer_phone ?: __('No phone') }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $creditor->created_at ? $creditor->created_at->format('d M Y h:i A') : '—' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-slate-600">{{ __('Qty') }}: {{ $creditor->quantity }}</p>
                                    <p class="text-base font-extrabold text-blue-700">TZS {{ number_format($creditor->total, 2) }}</p>
                                </div>
                            </div>

                            <div class="mt-2 text-xs text-slate-600">
                                @foreach($creditor->items as $item)
                                    <div class="flex items-center justify-between">
                                        <span>{{ $item->product->name ?? '—' }} × {{ $item->quantity }}</span>
                                        <span>TZS {{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-red-600 font-semibold">
                                    {{ __('Discount') }}: - TZS {{ number_format($creditor->discount_amount ?? 0, 2) }}
                                </span>
                                <form method="POST" action="{{ route('creditors.mark-paid', $creditor) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700"
                                    >
                                        {{ __('Mark As Paid') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-white rounded-2xl border border-slate-100 shadow p-4">
            <h2 class="text-lg font-bold text-slate-800 mb-3">{{ __('Recently Paid') }}</h2>

            @if($paidCreditors->isEmpty())
                <p class="text-sm text-slate-500">{{ __('No paid credit records yet.') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($paidCreditors as $creditor)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-bold text-emerald-800">{{ $creditor->customer_name }}</p>
                                    <p class="text-xs text-emerald-700">
                                        {{ __('Paid') }}: {{ $creditor->paid_at ? $creditor->paid_at->format('d M Y h:i A') : '—' }}
                                    </p>
                                    @if($creditor->sale_id)
                                        <p class="text-xs text-emerald-700">{{ __('Sale ID') }}: #{{ $creditor->sale_id }}</p>
                                    @endif
                                </div>
                                <p class="text-base font-extrabold text-emerald-700">TZS {{ number_format($creditor->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
