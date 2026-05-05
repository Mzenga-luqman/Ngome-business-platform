<?php

namespace App\Http\Controllers;

use App\Models\Creditor;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CreditorController extends Controller
{
    public function markPaid(Creditor $creditor): RedirectResponse
    {
        try {
            $user = auth()->user();

            DB::transaction(function () use ($creditor, $user): void {
                $creditor = Creditor::forAccount($user)
                    ->with('items')
                    ->lockForUpdate()
                    ->findOrFail($creditor->id);

                if ($creditor->status === 'paid') {
                    return;
                }

                if ($creditor->items->isEmpty()) {
                    throw new \Exception('Credit record has no items.');
                }

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
            });

            return back()->with('success', 'Credit marked as paid and converted to sale successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to mark credit as paid.', [
                'creditor_id' => $creditor->id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Unable to complete this action right now. Please try again.');
        }
    }
}
