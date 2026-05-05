<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;
use Illuminate\Support\Carbon;

class Dashboard extends Component
{
    public function getTodaysSalesProperty(): float
    {
        return Sale::forAccount(auth()->user())
            ->whereDate('sold_at', Carbon::today())
            ->sum('total');
    }

    public function getTotalProductsProperty(): int
    {
        return Product::forAccount(auth()->user())->count();
    }

    public function getLowStockProductsProperty()
    {
        return Product::forAccount(auth()->user())
            ->where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'todaysSales'      => $this->todaysSales,
            'totalProducts'    => $this->totalProducts,
            'lowStockProducts' => $this->lowStockProducts,
        ])->layout('layouts.app', ['title' => 'Dashboard', 'active' => 'dashboard']);
    }
}
