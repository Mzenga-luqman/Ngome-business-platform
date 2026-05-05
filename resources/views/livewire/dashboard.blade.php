<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Dashboard') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('Welcome back, Admin - here\'s what\'s happening today.') }}</p>
    </div>

    {{-- ===================== STAT CARDS ===================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

        {{-- Today's Sales --}}
        <a href="{{ route('sales', ['range' => 'today']) }}"
           class="group bg-white rounded-2xl shadow-lg shadow-blue-100/60 p-6 flex items-start gap-4
                hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="w-13 h-13 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-600/30 flex-shrink-0" style="width:52px;height:52px">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Today\'s Sales') }}</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1">
                    TZS {{ number_format($todaysSales, 2) }}
                </p>
                <p class="text-xs text-blue-500 mt-1 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    {{ __('View today\'s sales') }}
                </p>
            </div>
        </a>

        {{-- Total Products --}}
        <a href="{{ route('products') }}"
           class="group bg-white rounded-2xl shadow-lg shadow-emerald-100/60 p-6 flex items-start gap-4
                hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <div class="rounded-xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30 flex-shrink-0" style="width:52px;height:52px">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Products') }}</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalProducts }}</p>
                <p class="text-xs text-emerald-500 mt-1 font-medium">{{ __('In inventory') }}</p>
            </div>
        </a>

        {{-- Low Stock --}}
        <a href="{{ route('products', ['filter' => 'low-stock']) }}"
           class="group bg-white rounded-2xl shadow-lg shadow-red-100/60 p-6 flex items-start gap-4
                hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-slate-100
                focus:outline-none focus:ring-2 focus:ring-red-500
                {{ $lowStockProducts->count() > 0 ? 'ring-2 ring-red-200' : '' }}">
            <div class="rounded-xl {{ $lowStockProducts->count() > 0 ? 'bg-red-500' : 'bg-slate-400' }}
                        flex items-center justify-center shadow-lg flex-shrink-0
                        {{ $lowStockProducts->count() > 0 ? 'shadow-red-500/30' : '' }}"
                 style="width:52px;height:52px">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Low Stock') }}</p>
                <p class="text-2xl font-extrabold mt-1 {{ $lowStockProducts->count() > 0 ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $lowStockProducts->count() }}
                </p>
                <p class="text-xs mt-1 font-medium {{ $lowStockProducts->count() > 0 ? 'text-red-400' : 'text-slate-400' }}">
                    {{ __('Items <= 5 units') }}
                </p>
            </div>
        </a>
    </div>

    {{-- ===================== LOW STOCK TABLE ===================== --}}
    @if($lowStockProducts->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-red-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-red-700">{{ __('Low Stock Alert') }}</h2>
                <p class="text-xs text-red-400">{{ __('These products need restocking immediately') }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Product') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Stock') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($lowStockProducts as $product)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-slate-800">{{ $product->name }}</td>
                        <td class="px-6 py-3.5 text-slate-600">TZS {{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-3.5 font-bold text-red-600">{{ $product->quantity }}</td>
                        <td class="px-6 py-3.5">
                            @if($product->quantity === 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ __('Out of Stock') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ __('Critical') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow border border-slate-100 p-8 text-center">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="font-semibold text-slate-700">{{ __('All products are well stocked!') }}</p>
        <p class="text-sm text-slate-400 mt-1">{{ __('No low stock alerts at the moment.') }}</p>
    </div>
    @endif

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('pos') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl p-4 flex flex-col items-center gap-2
                  shadow-lg shadow-blue-600/30 hover:shadow-blue-700/40 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="text-xs font-semibold">{{ __('New Sale') }}</span>
        </a>
        <a href="{{ route('products') }}"
           class="bg-white hover:bg-slate-50 text-slate-700 rounded-xl p-4 flex flex-col items-center gap-2
                  shadow border border-slate-200 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-xs font-semibold">{{ __('Add Product') }}</span>
        </a>
        <a href="{{ route('sales') }}"
           class="bg-white hover:bg-slate-50 text-slate-700 rounded-xl p-4 flex flex-col items-center gap-2
                  shadow border border-slate-200 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                 d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
             <span class="text-xs font-semibold">{{ __('Reports') }}</span>
        </a>
         <a href="{{ route('finance') }}"
           class="bg-white hover:bg-slate-50 text-slate-700 rounded-xl p-4 flex flex-col items-center gap-2
                  shadow border border-slate-200 hover:-translate-y-0.5 transition-all duration-200">
             <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                 d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
             <span class="text-xs font-semibold">{{ __('Finance') }}</span>
        </a>
    </div>
</div>
