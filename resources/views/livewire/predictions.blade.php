<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Smart Predictions') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('AI-powered restocking alerts based on your average daily sales.') }}</p>
    </div>

    {{-- How it works banner --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-blue-800">{{ __('How predictions work') }}</p>
            <p class="text-xs text-blue-600 mt-0.5">
                Based on your average daily sales over the last 30 days, we estimate when each product will run out.
                <strong>Act before the warning reaches "critical"</strong> to avoid losing sales.
            </p>
        </div>
    </div>

    {{-- Predictions table --}}
    @php
        $urgencyMeta = [
            'critical' => ['label' => __('CRITICAL'),  'badge' => 'bg-red-100 text-red-700',    'row' => 'bg-red-50/60',    'icon' => '🔴'],
            'warning'  => ['label' => __('WARNING'),    'badge' => 'bg-orange-100 text-orange-700', 'row' => 'bg-orange-50/40', 'icon' => '🟠'],
            'moderate' => ['label' => __('MODERATE'),   'badge' => 'bg-amber-100 text-amber-700',  'row' => '',               'icon' => '🟡'],
            'safe'     => ['label' => __('SAFE'),       'badge' => 'bg-emerald-100 text-emerald-700','row' => '',              'icon' => '🟢'],
            'no_sales' => ['label' => __('NO DATA'),    'badge' => 'bg-slate-100 text-slate-500',  'row' => '',               'icon' => '⚪'],
        ];
    @endphp

    @if(empty($predictions))
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-10 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="font-semibold text-slate-700">{{ __('No products in stock to predict.') }}</p>
            <p class="text-sm text-slate-400 mt-1">{{ __('Add products to the inventory to see predictions here.') }}</p>
        </div>
    @else

    {{-- Alert summary bar --}}
    @php
        $criticalCount = collect($predictions)->where('urgency', 'critical')->count();
        $warningCount  = collect($predictions)->where('urgency', 'warning')->count();
        $restockCount  = collect($predictions)->where('restock_suggested', true)->count();
    @endphp
    @if($criticalCount > 0 || $warningCount > 0 || $restockCount > 0)
    <div class="flex flex-wrap gap-3">
        @if($criticalCount > 0)
        <div class="flex items-center gap-2 bg-red-100 border border-red-300 text-red-700 rounded-xl px-4 py-2 text-sm font-semibold">
            🔴 {{ $criticalCount }} product(s) running out in ≤ 2 days!
        </div>
        @endif
        @if($warningCount > 0)
        <div class="flex items-center gap-2 bg-orange-100 border border-orange-300 text-orange-700 rounded-xl px-4 py-2 text-sm font-semibold">
            🟠 {{ $warningCount }} product(s) running out within a week.
        </div>
        @endif
        @if($restockCount > 0)
        <div class="flex items-center gap-2 bg-violet-100 border border-violet-300 text-violet-700 rounded-xl px-4 py-2 text-sm font-semibold">
            🛒 {{ $restockCount }} product(s) have ≤ 10 units — restock suggested.
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Product') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Stock') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Avg/Day (30d)') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Days Until Empty') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Prediction') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($predictions as $p)
                    @php $meta = $urgencyMeta[$p['urgency']]; @endphp
                    <tr class="{{ $meta['row'] }} hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $p['product']->image_url }}"
                                     alt="{{ $p['product']->name }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0"/>
                                <span class="font-semibold text-slate-800">{{ $p['product']->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-bold {{ $p['product']->quantity <= 2 ? 'text-red-600' : ($p['product']->quantity <= 10 ? 'text-amber-600' : 'text-slate-700') }}">
                                    {{ __(':count units', ['count' => $p['product']->quantity]) }}
                                </span>
                                @if($p['restock_suggested'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-700 w-fit">
                                    {{ __('Restock Now') }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @if($p['avg_per_day'] > 0)
                                {{ __(':count units', ['count' => $p['avg_per_day']]) }}
                            @else
                                <span class="text-slate-400 italic">No sales</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold
                                   {{ $p['urgency'] === 'critical' ? 'text-red-600' : ($p['urgency'] === 'warning' ? 'text-orange-600' : 'text-slate-700') }}">
                            @if($p['days_left'] === null)
                                <span class="text-slate-400">—</span>
                            @elseif($p['days_left'] === 0)
                                {{ __('Today!') }}
                            @else
                                {{ __('~:days day(s)', ['days' => $p['days_left']]) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs max-w-xs space-y-1">
                            @if($p['urgency'] === 'critical')
                                <span class="font-semibold text-red-700">Restock "{{ $p['product']->name }}" immediately — may run out in {{ $p['days_left'] ?? 0 }} day(s)!</span>
                            @elseif($p['urgency'] === 'warning')
                                <span class="text-orange-700">Order "{{ $p['product']->name }}" soon — {{ $p['days_left'] }} days remaining.</span>
                            @elseif($p['urgency'] === 'moderate')
                                About {{ $p['days_left'] }} days of stock left.
                            @elseif($p['urgency'] === 'safe')
                                <span class="text-emerald-600">Well stocked.</span>
                            @else
                                <span class="text-slate-400 italic">No sales history — cannot predict.</span>
                            @endif
                            @if($p['restock_suggested'])
                            <div class="text-violet-700 font-semibold mt-1">
                                ⚠️ Only {{ $p['product']->quantity }} unit(s) left — place a restock order soon.
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $meta['badge'] }}">
                                {{ $meta['icon'] }} {{ $meta['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
