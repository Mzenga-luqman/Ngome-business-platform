<?php

namespace App\Livewire;

use App\Models\Creditor;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Creditors extends Component
{
    public string $successMessage = '';
    public string $errorMessage = '';

    public function markPaid(int $creditorId): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        try {
            $user = auth()->user();

            $result = DB::transaction(function () use ($creditorId, $user) {
                $creditor = Creditor::forAccount($user)
                    ->with('items')
                    ->lockForUpdate()
                    ->findOrFail($creditorId);

                if ($creditor->status === 'paid') {
                    return [
                        'ok' => true,
                        'message' => 'This credit record is already marked as paid.',
                    ];
                }

                if ($creditor->items->isEmpty()) {
                    throw new \Exception('Credit record has no items.');
                }

                // Validate stock before converting credit to a sale.
                foreach ($creditor->items as $item) {
                    $product = Product::forAccount($user)
                        ->whereKey($item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $product) {
                        throw new \Exception("Product not found for item #{$item->id}.");
                    }

                    if ($product->quantity < $item->quantity) {
                        throw new \Exception(
                            "Insufficient stock for {$product->name}. Available: {$product->quantity}"
                        );
                    }
                }

                $sale = Sale::create([
                    'account_owner_id' => $user->accountOwnerId(),
                    'product_id' => null,
                    'user_id' => auth()->id(),
                    'quantity' => 0,
                    'total' => 0,
                    'sold_at' => Carbon::now(),
                ]);

                foreach ($creditor->items as $item) {
                    $product = Product::forAccount($user)
                        ->whereKey($item->product_id)
                        ->lockForUpdate()
                        ->first();

                    $product->decrement('quantity', $item->quantity);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }

                $sale->update([
                    'quantity' => (int) $creditor->quantity,
                    'subtotal' => round((float) ($creditor->subtotal ?? 0), 2),
                    'discount_type' => $creditor->discount_type,
                    'discount_value' => round((float) ($creditor->discount_value ?? 0), 2),
                    'discount_amount' => round((float) ($creditor->discount_amount ?? 0), 2),
                    'total' => round((float) $creditor->total, 2),
                ]);

                $creditor->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                ]);

                return [
                    'ok' => true,
                    'message' => "Credit marked as paid and converted to Sale #{$sale->id}.",
                ];
            });

            $this->successMessage = $result['message'];
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $unpaid = Creditor::forAccount(auth()->user())
            ->with(['items.product'])
            ->where('status', 'unpaid')
            ->orderByDesc('created_at')
            ->get();

        $paid = Creditor::forAccount(auth()->user())
            ->with(['items.product'])
            ->where('status', 'paid')
            ->orderByDesc('paid_at')
            ->limit(100)
            ->get();

        return view('livewire.creditors', [
            'unpaidCreditors' => $unpaid,
            'paidCreditors' => $paid,
        ])->layout('layouts.app', ['title' => 'Creditors', 'active' => 'creditors']);
    }
}
