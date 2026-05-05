<?php

namespace App\Livewire;

use App\Models\Sale;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Sales extends Component
{
    public string $range = 'all';

    public function mount(): void
    {
        $range = request()->query('range', 'all');
        $this->range = in_array($range, ['all', 'today'], true) ? $range : 'all';
    }

    public function render()
    {
        $salesQuery = Sale::forAccount(auth()->user())
            ->with(['product', 'items.product', 'user'])
            ->orderByDesc('sold_at');

        if ($this->range === 'today') {
            $salesQuery->whereDate('sold_at', Carbon::today());
        }

        return view('livewire.sales', [
            'sales' => $salesQuery->get(),
        ])->layout('layouts.app', ['title' => 'Sales History', 'active' => 'sales']);
    }
}
