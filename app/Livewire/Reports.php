<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reports extends Component
{
    public string $period = 'this_month'; // this_month | last_month | last_7_days | last_30_days

    public function getTopProductsProperty(): array
    {
        return Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->with('product')
            ->whereBetween('sold_at', [$this->dateRange()['start'], $this->dateRange()['end']])
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function getLeastProductsProperty(): array
    {
        return Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->with('product')
            ->whereBetween('sold_at', [$this->dateRange()['start'], $this->dateRange()['end']])
            ->groupBy('product_id')
            ->orderBy('total_qty')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function getDailySalesChartDataProperty(): array
    {
        $range = $this->dateRange();
        $sales = Sale::forAccount(auth()->user())
            ->select(DB::raw('DATE(sold_at) as day'), DB::raw('SUM(total) as daily_total'))
            ->whereBetween('sold_at', [$range['start'], $range['end']])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return [
            'labels' => $sales->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('d M'))->values()->toArray(),
            'data'   => $sales->pluck('daily_total')->map(fn ($v) => round((float) $v, 2))->values()->toArray(),
        ];
    }

    public function getProductSalesChartDataProperty(): array
    {
        $top = Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->whereBetween('sold_at', [$this->dateRange()['start'], $this->dateRange()['end']])
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        return [
            'labels' => $top->map(fn ($s) => $s->product?->name ?? 'Deleted')->values()->toArray(),
            'data'   => $top->pluck('total_qty')->values()->toArray(),
        ];
    }

    public function getTotalRevenueProperty(): float
    {
        $range = $this->dateRange();
        return (float) Sale::forAccount(auth()->user())
            ->whereBetween('sold_at', [$range['start'], $range['end']])
            ->sum('total');
    }

    public function getTotalTransactionsProperty(): int
    {
        $range = $this->dateRange();
        return Sale::forAccount(auth()->user())
            ->whereBetween('sold_at', [$range['start'], $range['end']])
            ->count();
    }

    public function exportCsv(): StreamedResponse
    {
        $range  = $this->dateRange();
        $sales  = Sale::forAccount(auth()->user())
            ->with('product')
            ->whereBetween('sold_at', [$range['start'], $range['end']])
            ->orderByDesc('sold_at')
            ->get();

        $filename = 'sales-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($sales) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Product', 'Quantity', 'Total (TZS)']);
            foreach ($sales as $sale) {
                fputcsv($out, [
                    $sale->sold_at->format('Y-m-d H:i'),
                    $sale->product?->name ?? 'Deleted Product',
                    $sale->quantity,
                    number_format((float) $sale->total, 2, '.', ''),
                ]);
            }
            fclose($out);
        }, $filename);
    }

    private function dateRange(): array
    {
        return match ($this->period) {
            'last_7_days'  => ['start' => now()->subDays(7)->startOfDay(),  'end' => now()->endOfDay()],
            'last_30_days' => ['start' => now()->subDays(30)->startOfDay(), 'end' => now()->endOfDay()],
            'last_month'   => ['start' => now()->subMonth()->startOfMonth(), 'end' => now()->subMonth()->endOfMonth()],
            default        => ['start' => now()->startOfMonth(), 'end' => now()->endOfMonth()],
        };
    }

    public function render()
    {
        return view('livewire.reports', [
            'topProducts'           => $this->topProducts,
            'leastProducts'         => $this->leastProducts,
            'dailySalesChartData'   => $this->dailySalesChartData,
            'productSalesChartData' => $this->productSalesChartData,
            'totalRevenue'          => $this->totalRevenue,
            'totalTransactions'     => $this->totalTransactions,
        ])->layout('layouts.app', ['title' => 'Reports & Analytics', 'active' => 'reports']);
    }
}
