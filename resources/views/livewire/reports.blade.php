<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Reports & Analytics') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Business performance overview and sales trends.') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Period filter --}}
            <select wire:model.live="period"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="this_month">{{ __('This Month') }}</option>
                <option value="last_7_days">{{ __('Last 7 Days') }}</option>
                <option value="last_30_days">{{ __('Last 30 Days') }}</option>
                <option value="last_month">{{ __('Last Month') }}</option>
            </select>
            {{-- Export CSV --}}
            <button wire:click="exportCsv"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('Export CSV') }}
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-600/25 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Revenue') }}</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-0.5">TZS {{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-500 flex items-center justify-center shadow-lg shadow-violet-500/25 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Transactions') }}</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ number_format($totalTransactions) }}</p>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
   <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Daily Sales Trend (Line) --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
            <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                {{ __('Daily Sales Trend') }}
            </h2>
            <div class="relative h-56">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        {{-- Product vs Sales (Bar) --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
            <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-violet-500 inline-block"></span>
                {{ __('Top Products by Units Sold') }}
            </h2>
            <div class="relative h-56">
                <canvas id="productChart"></canvas>
            </div>
        </div>
    </div>
          

    {{-- Top & Least Products --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Top 5 --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-emerald-800">{{ __('Top 5 Best Sellers') }}</h2>
                    <p class="text-xs text-emerald-500">{{ __('Highest units sold this period') }}</p>
                </div>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($topProducts as $i => $row)
                <div class="flex items-center justify-between px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center
                                     {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $i + 1 }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $row['product']['name'] ?? __('Deleted') }}</p>
                            <p class="text-xs text-slate-400">TZS {{ number_format($row['total_revenue'], 2) }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">{{ __(':count units', ['count' => number_format($row['total_qty'])]) }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-slate-400">{{ __('No sales data for this period.') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Least 5 --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-amber-50 border-b border-amber-100">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-amber-800">{{ __('Least Selling Products') }}</h2>
                    <p class="text-xs text-amber-500">{{ __('Lowest units sold this period') }}</p>
                </div>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($leastProducts as $i => $row)
                <div class="flex items-center justify-between px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-xs font-bold flex items-center justify-center text-slate-500">
                            {{ $i + 1 }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $row['product']['name'] ?? __('Deleted') }}</p>
                            <p class="text-xs text-slate-400">TZS {{ number_format($row['total_revenue'], 2) }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-amber-600">{{ __(':count units', ['count' => number_format($row['total_qty'])]) }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-slate-400">{{ __('No sales data for this period.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const dailyData   = @json($dailySalesChartData);
    const productData = @json($productSalesChartData);

    const baseLineOptions = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
            y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8' } }
        }
    };

    // Daily trend line chart
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.labels,
                datasets: [{
                    data: dailyData.data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: baseLineOptions
        });
    }

    // Product bar chart — products on X axis, quantity sold on Y axis
    const productCtx = document.getElementById('productChart');
    if (productCtx) {
        new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: productData.labels,
                datasets: [{
                    data: productData.data,
                    backgroundColor: [
                        'rgba(139,92,246,0.8)','rgba(99,102,241,0.8)','rgba(59,130,246,0.8)',
                        'rgba(20,184,166,0.8)','rgba(34,197,94,0.8)','rgba(234,179,8,0.8)',
                        'rgba(249,115,22,0.8)','rgba(239,68,68,0.8)'
                    ],
                    borderRadius: 6,
                    borderWidth: 0,
                    maxBarThickness: 48
                }]
            },
            options: {
                indexAxis: 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.parsed.y + ' {{ __('units') }}'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#94a3b8',
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            color: '#94a3b8',
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: '{{ __('Units Sold') }}',
                            color: '#94a3b8',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
