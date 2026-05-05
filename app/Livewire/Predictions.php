<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Predictions extends Component
{
    /**
     * Compute average daily sales for each product over the last 30 days,
     * then estimate how many days before stock runs out.
     */
    public function getPredictionsProperty(): array
    {
        $lookbackDays = 30;

        // Average daily sales per product
        $salesData = Sale::forAccount(auth()->user())
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->where('sold_at', '>=', now()->subDays($lookbackDays))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Include zero-stock products so they appear as critical restock alerts
        $products = Product::forAccount(auth()->user())
            ->where('quantity', '>=', 0)
            ->orderBy('name')
            ->get();

        $result = [];

        foreach ($products as $product) {
            $soldEntry        = $salesData->get($product->id);
            $totalSold        = $soldEntry ? (float) $soldEntry->total_sold : 0;
            $avgPerDay        = round($totalSold / $lookbackDays, 2);
            $restockSuggested = $product->quantity <= 10;

            if ($avgPerDay <= 0) {
                $result[] = [
                    'product'          => $product,
                    'avg_per_day'      => 0,
                    'days_left'        => null,
                    'status'           => 'no_sales',
                    'urgency'          => 'no_sales',
                    'restock_suggested'=> $restockSuggested,
                ];
                continue;
            }

            $daysLeft = (int) floor($product->quantity / $avgPerDay);

            $urgency = match (true) {
                $daysLeft <= 2  => 'critical',
                $daysLeft <= 7  => 'warning',
                $daysLeft <= 14 => 'moderate',
                default         => 'safe',
            };

            $result[] = [
                'product'          => $product,
                'avg_per_day'      => $avgPerDay,
                'days_left'        => $daysLeft,
                'status'           => 'active',
                'urgency'          => $urgency,
                'restock_suggested'=> $restockSuggested,
            ];
        }

        // Sort: critical first
        usort($result, fn ($a, $b) => [
            'critical' => 0, 'warning' => 1, 'moderate' => 2, 'safe' => 3, 'no_sales' => 4
        ][$a['urgency']] <=> [
            'critical' => 0, 'warning' => 1, 'moderate' => 2, 'safe' => 3, 'no_sales' => 4
        ][$b['urgency']]);

        return $result;
    }

    public function render()
    {
        return view('livewire.predictions', [
            'predictions' => $this->predictions,
        ])->layout('layouts.app', ['title' => 'Smart Predictions', 'active' => 'predictions']);
    }
}
