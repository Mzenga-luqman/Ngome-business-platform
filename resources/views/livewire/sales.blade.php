<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Sales History') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('All completed transactions.') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ $sales->count() }} {{ $range === 'today' ? __('sales today') : __('total sales') }}
                </div>
                @if($range === 'today')
                    <a href="{{ route('sales') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-xl px-4 py-2.5 hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('Clear today filter') }}
                    </a>
                @endif
            </div>
            <a href="{{ route('pos') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/30
                      hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('New Sale') }}
            </a>
        </div>
    </div>

    {{-- Summary Row --}}
    @if($sales->count() > 0)
    @php
        $appTimezone = config('app.timezone');
        $totalRevenue = $sales->sum('total');
        $totalDiscounts = $sales->sum(fn($s) => (float) ($s->discount_amount ?? 0));
        $totalUnits   = $sales->sum('quantity');
        $todaySales   = $sales->filter(fn($s) => $s->sold_at && $s->sold_at->copy()->timezone($appTimezone)->isToday())->sum('total');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow p-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Revenue') }}</p>
            <p class="text-lg font-extrabold text-slate-800 mt-1">TZS {{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow p-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Today\'s Revenue') }}</p>
            <p class="text-lg font-extrabold text-blue-600 mt-1">TZS {{ number_format($todaySales, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow p-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Discounts Given') }}</p>
            <p class="text-lg font-extrabold text-red-600 mt-1">TZS {{ number_format($totalDiscounts, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow p-4 col-span-2 sm:col-span-1">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Units Sold') }}</p>
            <p class="text-lg font-extrabold text-slate-800 mt-1">{{ number_format($totalUnits) }}</p>
        </div>
    </div>
    @endif

    {{-- ===================== SALES TABLE ===================== --}}
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('All Transactions') }}
            </h2>
        </div>

        @if($sales->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="font-semibold text-slate-600">{{ __('No sales recorded yet') }}</p>
            <p class="text-sm text-slate-400 mt-1">{{ __('Go to the POS page to make your first sale.') }}</p>
            <a href="{{ route('pos') }}"
               class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold
                      text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/30
                      hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('Start Selling') }}
            </a>
        </div>
        @else

        {{-- ===== MOBILE: card list (hidden on md+) ===== --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($sales as $index => $sale)
            @php
                $hasItems = $sale->items && $sale->items->count() > 0;
                $displayProduct = $hasItems
                    ? ($sale->items->first()?->product ?? null)
                    : ($sale->product ?? null);
                $moreCount = $hasItems ? max($sale->items->count() - 1, 0) : 0;
                $saleName = $displayProduct?->name ?? '—';
            @endphp
            <div class="flex items-center gap-3 p-4">
                @if($displayProduct)
                    <img src="{{ $displayProduct->image_url }}" alt="{{ $saleName }}"
                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0"/>
                @else
                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $saleName }}@if($moreCount > 0) <span class="text-xs font-normal text-slate-400">+{{ $moreCount }} more</span>@endif</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $sale->sold_at ? $sale->sold_at->copy()->timezone($appTimezone)->format('d M Y · h:i A') : '—' }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700">× {{ $sale->quantity }}</span>
                        <span class="text-sm font-black text-slate-800">TZS {{ number_format($sale->total, 2) }}</span>
                    </div>
                    @if((float)($sale->discount_amount ?? 0) > 0)
                        <p class="text-xs text-red-600 font-semibold mt-1">{{ __('Discount') }}: - TZS {{ number_format($sale->discount_amount, 2) }}</p>
                    @endif
                </div>
                <a href="{{ route('receipt', ['saleId' => $sale->id]) }}"
                   target="_blank"
                   onclick="window.open(this.href, 'receipt', 'width=400,height=600'); return false;"
                   class="flex-shrink-0 flex h-11 w-11 items-center justify-center rounded-xl bg-orange-600 text-white hover:bg-orange-700 transition-colors text-base">
                    🖨
                </a>
            </div>
            @endforeach
            <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-t-2 border-slate-200">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ __('Grand Total') }}</span>
                <span class="font-extrabold text-blue-700">TZS {{ number_format($sales->sum('total'), 2) }}</span>
            </div>
        </div>

        {{-- ===== DESKTOP: full table (hidden on mobile) ===== --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Product') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Qty') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Discount') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Total') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Date & Time') }}</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Receipt') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($sales as $index => $sale)
                        @if($sale->items && $sale->items->count() > 0)
                            {{-- Multi-item sale: display one row per sale --}}
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $firstItem = $sale->items->first();
                                            $moreItemsCount = max($sale->items->count() - 1, 0);
                                        @endphp
                                        @if($firstItem && $firstItem->product)
                                            <img src="{{ $firstItem->product->image_url }}"
                                                 alt="{{ $firstItem->product->name }}"
                                                 class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-sm flex-shrink-0"/>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-slate-800 truncate">
                                                    {{ $firstItem->product->name }}
                                                </p>
                                                <p class="text-xs text-slate-400">
                                                    @if($moreItemsCount > 0)
                                                        + {{ __(':count more item(s)', ['count' => $moreItemsCount]) }}
                                                    @else
                                                        {{ __('Single item in sale') }}
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-slate-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700">
                                        × {{ $sale->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold {{ (float)($sale->discount_amount ?? 0) > 0 ? 'text-red-600' : 'text-slate-400' }}">
                                    @if((float)($sale->discount_amount ?? 0) > 0)
                                        - TZS {{ number_format($sale->discount_amount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    TZS {{ number_format($sale->total, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($sale->sold_at)
                                        <div>
                                            <p class="text-slate-700 font-medium">{{ $sale->sold_at->copy()->timezone($appTimezone)->format('d M Y') }}</p>
                                            <p class="text-slate-400 text-xs">{{ $sale->sold_at->copy()->timezone($appTimezone)->format('h:i A') }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('receipt', ['saleId' => $sale->id]) }}"
                                       target="_blank"
                                       onclick="window.open(this.href, 'receipt', 'width=400,height=600'); return false;"
                                       class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-3 py-2 text-xs font-bold text-white hover:bg-orange-700 transition-colors">
                                        {{ __('Receipt') }}
                                    </a>
                                </td>
                            </tr>
                        @else
                            {{-- Legacy single-item sale: use old $sale->product relationship --}}
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($sale->product)
                                            <img src="{{ $sale->product->image_url }}"
                                                 alt="{{ $sale->product->name }}"
                                                 class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-sm flex-shrink-0"/>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-slate-800 truncate">
                                                    {{ $sale->product->name }}
                                                </p>
                                                <p class="text-xs text-slate-400">{{ __('Product sold') }}</p>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-slate-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700">
                                        × {{ $sale->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold {{ (float)($sale->discount_amount ?? 0) > 0 ? 'text-red-600' : 'text-slate-400' }}">
                                    @if((float)($sale->discount_amount ?? 0) > 0)
                                        - TZS {{ number_format($sale->discount_amount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    TZS {{ number_format($sale->total, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($sale->sold_at)
                                        <div>
                                            <p class="text-slate-700 font-medium">{{ $sale->sold_at->copy()->timezone($appTimezone)->format('d M Y') }}</p>
                                            <p class="text-slate-400 text-xs">{{ $sale->sold_at->copy()->timezone($appTimezone)->format('h:i A') }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('receipt', ['saleId' => $sale->id]) }}"
                                       target="_blank"
                                       onclick="window.open(this.href, 'receipt', 'width=400,height=600'); return false;"
                                       class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-3 py-2 text-xs font-bold text-white hover:bg-orange-700 transition-colors">
                                        {{ __('Receipt') }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                        <td colspan="2" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">{{ __('Grand Total') }}</td>
                        <td class="px-6 py-4 font-bold text-blue-700">{{ $sales->sum('quantity') }}</td>
                        <td class="px-6 py-4 font-bold text-red-600">
                            - TZS {{ number_format($sales->sum('discount_amount'), 2) }}
                        </td>
                        <td class="px-6 py-4 font-extrabold text-blue-700">
                            TZS {{ number_format($sales->sum('total'), 2) }}
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
