<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockInsights extends Component
{
    public int $deadStockDays = 14; // products not sold in last N days

    public function getFastMovingProductsProperty()
    {
        return Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('product')
            ->where('sold_at', '>=', now()->subDays(7))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->filter(fn ($s) => $s->product !== null);
    }

    public function getDeadStockProductsProperty()
    {
        // Products that have NOT been sold in the last N days
        $recentlySoldIds = Sale::forAccount(auth()->user())
            ->where('sold_at', '>=', now()->subDays($this->deadStockDays))
            ->pluck('product_id')
            ->unique();

        return Product::forAccount(auth()->user())
            ->whereNotIn('id', $recentlySoldIds)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->get();
    }

    public function getLowStockProductsProperty()
    {
        return Product::forAccount(auth()->user())
            ->where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->get();
    }

    public function getOutOfStockProductsProperty()
    {
        return Product::forAccount(auth()->user())->where('quantity', 0)->get();
    }

    public function getSlowMovingProductsProperty()
    {
        // Products sold at least once but very little (< 5 units) in the last 30 days
        return Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('product')
            ->where('sold_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->having('total_sold', '<', 5)
            ->orderBy('total_sold')
            ->get()
            ->filter(fn ($s) => $s->product !== null);
    }

    public function render()
    {
        return view('livewire.stock-insights', [
            'fastMoving'   => $this->fastMovingProducts,
            'deadStock'    => $this->deadStockProducts,
            'lowStock'     => $this->lowStockProducts,
            'outOfStock'   => $this->outOfStockProducts,
            'slowMoving'   => $this->slowMovingProducts,
        ])->layout('layouts.app', ['title' => 'Stock Insights', 'active' => 'stock-insights']);
    }
}
