<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Stock Insights') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Understand your inventory health - fast movers, dead stock, and alerts.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs text-slate-500 font-semibold">{{ __('Dead stock threshold:') }}</label>
            <select wire:model.live="deadStockDays"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="7">{{ __('7 days') }}</option>
                <option value="14">{{ __('14 days') }}</option>
                <option value="30">{{ __('30 days') }}</option>
            </select>
        </div>
    </div>

    {{-- Alert Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
            <p class="text-2xl font-extrabold text-red-600">{{ $outOfStock->count() }}</p>
            <p class="text-xs font-semibold text-red-500 mt-1">{{ __('Out of Stock') }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-center">
            <p class="text-2xl font-extrabold text-amber-600">{{ $lowStock->count() }}</p>
            <p class="text-xs font-semibold text-amber-500 mt-1">{{ __('Low Stock (<=5)') }}</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-600">{{ $deadStock->count() }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-1">{{ __('Dead Stock (:days d)', ['days' => $deadStockDays]) }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
            <p class="text-2xl font-extrabold text-emerald-600">{{ $fastMoving->count() }}</p>
            <p class="text-xs font-semibold text-emerald-500 mt-1">{{ __('Fast Moving (7d)') }}</p>
        </div>
    </div>

    {{-- Fast Moving Products --}}
    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-emerald-50 border-b border-emerald-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-emerald-800">{{ __('Fast Moving Products') }}</h2>
                <p class="text-xs text-emerald-500">{{ __('Sold most units in the last 7 days') }}</p>
            </div>
        </div>
        @if($fastMoving->isEmpty())
            <div class="px-6 py-6 text-center text-sm text-slate-400">{{ __('No sales in the last 7 days.') }}</div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($fastMoving as $item)
            <div class="flex items-center justify-between px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                         class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0"/>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $item->product->name }}</p>
                        <p class="text-xs text-slate-400">{{ __('Stock: :count remaining', ['count' => $item->product->quantity]) }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                    {{ __(':count sold', ['count' => $item->total_sold]) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Dead Stock Products --}}
    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-slate-50 border-b border-slate-200">
            <div class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-700">{{ __('Dead Stock (not sold in :days days)', ['days' => $deadStockDays]) }}</h2>
                <p class="text-xs text-slate-400">{{ __('These products are sitting idle - consider promotions or discounts') }}</p>
            </div>
        </div>
        @if($deadStock->isEmpty())
            <div class="px-6 py-6 text-center text-sm text-slate-400">
                <span class="text-emerald-600 font-semibold">{{ __('All products have moved recently.') }}</span>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Product') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Stock Qty') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Stock Value') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Recommendation') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($deadStock as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0"/>
                                <span class="font-semibold text-slate-800">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-slate-600">TZS {{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-3.5 font-bold text-slate-700">{{ $product->quantity }}</td>
                        <td class="px-6 py-3.5 font-semibold text-slate-600">
                            TZS {{ number_format($product->price * $product->quantity, 2) }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                {{ __('Consider discount') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Slow Moving --}}
    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-amber-50 border-b border-amber-100">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-amber-800">{{ __('Slow Moving Products (last 30 days, < 5 units)') }}</h2>
                <p class="text-xs text-amber-500">{{ __('Products that sold but very slowly') }}</p>
            </div>
        </div>
        @if($slowMoving->isEmpty())
            <div class="px-6 py-6 text-center text-sm text-slate-400">{{ __('No slow-moving products detected.') }}</div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($slowMoving as $item)
            <div class="flex items-center justify-between px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                         class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0"/>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $item->product->name }}</p>
                        <p class="text-xs text-slate-400">{{ __('Stock: :count remaining', ['count' => $item->product->quantity]) }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                    {{ __(':count sold (30d)', ['count' => $item->total_sold]) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Out of Stock --}}
    @if($outOfStock->count() > 0)
    <div class="bg-white rounded-2xl shadow border border-red-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-red-700">{{ __('Out of Stock') }}</h2>
                <p class="text-xs text-red-400">{{ __('These products need immediate restocking') }}</p>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($outOfStock as $product)
            <div class="flex items-center justify-between px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0"/>
                    <span class="font-semibold text-slate-800 text-sm">{{ $product->name }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ __('Out of Stock') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
