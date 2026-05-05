<?php

namespace App\Livewire;

use App\Models\Creditor;
use App\Models\CreditorItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pos extends Component
{
    public string $barcodeInput = '';
    public array $cart = []; // [['product_id' => id, 'quantity' => qty], ...]
    public ?int $selectedQuickProductId = null;
    public string $paymentMode = 'cash'; // cash|credit
    public string $creditCustomerName = '';
    public string $creditCustomerPhone = '';
    public string $discountType = 'none'; // none|5|10|manual
    public string $manualDiscountAmount = '';
    public string $successMessage = '';
    public string $errorMessage = '';
    public string $lastScannedProduct = '';
    public ?int $lastSaleId = null;

    public function setDiscountType(string $type): void
    {
        if (!in_array($type, ['none', '5', '10', 'manual'], true)) {
            return;
        }

        $this->discountType = $type;

        if ($type !== 'manual') {
            $this->manualDiscountAmount = '';
        }
    }

    public function updatedDiscountType($value): void
    {
        $value = (string) $value;
        if (! in_array($value, ['none', '5', '10', 'manual'], true)) {
            $this->discountType = 'none';
            $this->manualDiscountAmount = '';
            return;
        }

        if ($value !== 'manual') {
            $this->manualDiscountAmount = '';
        }
    }

    public function updatedPaymentMode($value): void
    {
        $value = (string) $value;
        if (! in_array($value, ['cash', 'credit'], true)) {
            $this->paymentMode = 'cash';
            $this->creditCustomerName = '';
            $this->creditCustomerPhone = '';
            return;
        }

        if ($value !== 'credit') {
            $this->creditCustomerName = '';
            $this->creditCustomerPhone = '';
        }
    }

    public function updatedManualDiscountAmount($value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $amount = max(0, (float) $value);
        $this->manualDiscountAmount = (string) round($amount, 2);
    }

    public function submitBarcodeScan(): void
    {
        $barcode = trim($this->barcodeInput);

        if ($barcode === '') {
            return;
        }

        $this->addProductByBarcode($barcode);
    }

    public function addProductByBarcode($barcode, bool $silentIfNotFound = false): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        $barcode = trim((string) $barcode);
        if ($barcode === '') {
            return;
        }

        $product = Product::forAccount(auth()->user())
            ->where('barcode', $barcode)
            ->first();

        if (!$product) {
            if ($silentIfNotFound) {
                return;
            }

            $this->errorMessage = "Product not found: {$barcode}";
            return;
        }

        if ($product->quantity <= 0) {
            $this->errorMessage = "Out of stock: {$product->name}";
            return;
        }

        // Check if product already in cart
        $cartIndex = null;
        foreach ($this->cart as $idx => $item) {
            if ($item['product_id'] === $product->id) {
                $cartIndex = $idx;
                break;
            }
        }

        if ($cartIndex !== null) {
            // Increment quantity
            $this->cart[$cartIndex]['quantity']++;
            if ($this->cart[$cartIndex]['quantity'] > $product->quantity) {
                $this->cart[$cartIndex]['quantity'] = $product->quantity;
                $this->errorMessage = "Reached max available stock: {$product->quantity} units";
            }
        } else {
            // Add to cart
            $this->cart[] = [
                'product_id' => $product->id,
                'quantity' => 1,
            ];
        }

        $this->lastScannedProduct = $product->name;
        $this->successMessage = "✓ Added: {$product->name}";
        $this->barcodeInput = '';
        $this->dispatch('barcode-scan-complete');
    }

    public function updatedBarcodeInput($value): void
    {
        $barcode = trim((string) $value);

        if ($barcode === '') {
            return;
        }

        // Auto-add only when a full known barcode is present.
        $this->addProductByBarcode($barcode, true);
    }

    public function addSelectedProduct(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        if (! $this->selectedQuickProductId) {
            return;
        }

        $product = Product::forAccount(auth()->user())
            ->whereKey($this->selectedQuickProductId)
            ->first();

        if (! $product) {
            $this->errorMessage = 'Selected product was not found.';
            return;
        }

        $this->addProductByBarcode((string) ($product->barcode ?: ''), false);

        if (! $product->barcode) {
            // Fallback for products without barcode.
            $this->addProductById($product->id);
        }

        $this->selectedQuickProductId = null;
    }

    private function addProductById(int $productId): void
    {
        $product = Product::forAccount(auth()->user())->find($productId);
        if (! $product) {
            $this->errorMessage = 'Product not found.';
            return;
        }

        if ($product->quantity <= 0) {
            $this->errorMessage = "Out of stock: {$product->name}";
            return;
        }

        $cartIndex = null;
        foreach ($this->cart as $idx => $item) {
            if (($item['product_id'] ?? null) === $product->id) {
                $cartIndex = $idx;
                break;
            }
        }

        if ($cartIndex !== null) {
            $nextQty = (int) ($this->cart[$cartIndex]['quantity'] ?? 0) + 1;
            $this->cart[$cartIndex]['quantity'] = min($nextQty, (int) $product->quantity);
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'quantity' => 1,
            ];
        }

        $this->lastScannedProduct = $product->name;
        $this->successMessage = "✓ Added: {$product->name}";
    }

    public function syncCart(): void
    {
        $nextCart = [];
        $user = auth()->user();

        foreach ($this->cart as $item) {
            $remove = (bool) ($item['remove'] ?? false);
            if ($remove) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            $product = Product::forAccount($user)->find($productId);
            if (! $product || $product->quantity <= 0) {
                continue;
            }

            $nextCart[] = [
                'product_id' => $productId,
                'quantity' => min($quantity, (int) $product->quantity),
            ];
        }

        $this->cart = array_values($nextCart);
    }

    public function updateCartItemQuantity($productId, $quantity): void
    {
        $quantity = max(1, (int) $quantity);

        $product = Product::forAccount(auth()->user())->find($productId);
        if ($product && $quantity > $product->quantity) {
            $this->errorMessage = "Cannot exceed stock: {$product->quantity} units";
            return;
        }

        foreach ($this->cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }

    public function removeCartItem($productId): void
    {
        $this->cart = array_values(
            array_filter($this->cart, fn ($item) => $item['product_id'] !== $productId)
        );
    }

    public function getCartSubtotalProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $product = Product::forAccount(auth()->user())->find($item['product_id']);
            if ($product) {
                $total += (float) $product->price * $item['quantity'];
            }
        }

        return round($total, 2);
    }

    public function getDiscountPercentProperty(): float
    {
        return $this->effectiveDiscountPercent();
    }

    public function getDiscountAmountProperty(): float
    {
        $subtotal = $this->cartSubtotal;
        if ($subtotal <= 0) {
            return 0;
        }

        return $this->calculateDiscountAmount($subtotal);
    }

    public function getCartTotalProperty(): float
    {
        return round(max(0, $this->cartSubtotal - $this->discountAmount), 2);
    }

    public function getCartItemsCountProperty()
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function setPaymentMode(string $mode): void
    {
        if (! in_array($mode, ['cash', 'credit'], true)) {
            return;
        }

        $this->paymentMode = $mode;

        if ($mode !== 'credit') {
            $this->creditCustomerName = '';
            $this->creditCustomerPhone = '';
        }
    }

    public function completeSale(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        if (empty($this->cart)) {
            $this->errorMessage = 'Cart is empty. Add products before checkout.';
            return;
        }

        if ($this->paymentMode === 'credit') {
            $this->completeCreditSale();
            return;
        }

        try {
            $user = auth()->user();
            $saleSummary = DB::transaction(function () {
                $user = auth()->user();

                $sale = Sale::create([
                    'account_owner_id' => $user->accountOwnerId(),
                    'product_id' => null,  // Will use SaleItems instead
                    'user_id'    => auth()->id(),
                    'quantity'   => 0,
                    'total'      => 0,
                    'sold_at'    => Carbon::now(),
                ]);

                $subtotalPrice = 0;
                $totalQty = 0;
                $itemDescriptions = [];

                foreach ($this->cart as $cartItem) {
                    $product = Product::forAccount($user)
                        ->whereKey($cartItem['product_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new \Exception("Product {$cartItem['product_id']} not found");
                    }

                    if ($product->quantity < $cartItem['quantity']) {
                        throw new \Exception(
                            "Insufficient stock for {$product->name}. Available: {$product->quantity}"
                        );
                    }

                    $itemTotal = round((float) $product->price * $cartItem['quantity'], 2);

                    // Create SaleItem
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $cartItem['quantity'],
                        'price'      => $product->price,
                    ]);

                    // Deduct stock
                    $product->decrement('quantity', $cartItem['quantity']);

                    $subtotalPrice += $itemTotal;
                    $totalQty += $cartItem['quantity'];
                    $itemDescriptions[] = "{$cartItem['quantity']}× {$product->name}";
                }

                $discountPercent = $this->effectiveDiscountPercent();
                $discountAmount = $this->calculateDiscountAmount($subtotalPrice);
                $finalTotal = round(max(0, $subtotalPrice - $discountAmount), 2);

                $discountKind = 'none';
                $discountValue = 0;
                if ($discountAmount > 0) {
                    if ($this->discountType === 'manual') {
                        $discountKind = 'amount';
                        $discountValue = $discountAmount;
                    } else {
                        $discountKind = 'percent';
                        $discountValue = $discountPercent;
                    }
                }

                // Update sale totals
                $sale->update([
                    'quantity' => $totalQty,
                    'subtotal' => round($subtotalPrice, 2),
                    'discount_type' => $discountKind,
                    'discount_value' => round($discountValue, 2),
                    'discount_amount' => round($discountAmount, 2),
                    'total'    => $finalTotal,
                ]);

                $message = "✓ Sale completed! {$totalQty} items, TZS " . number_format($finalTotal, 2);
                if ($discountAmount > 0) {
                    if ($this->discountType === 'manual') {
                        $message .= " (Discount: TZS " . number_format($discountAmount, 2) . ")";
                    } else {
                        $message .= " (Discount " . number_format($discountPercent, 2) . "%: TZS " . number_format($discountAmount, 2) . ")";
                    }
                }

                return [
                    'ok' => true,
                    'sale_id' => $sale->id,
                    'message' => $message,
                    'items' => $itemDescriptions,
                ];
            });

            $this->lastSaleId = (int) $saleSummary['sale_id'];
            $this->successMessage = $saleSummary['message'] . ' Receipt is ready.';
            $this->cart = [];
            $this->barcodeInput = '';
            $this->discountType = 'none';
            $this->manualDiscountAmount = '';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('POS cash sale failed.', [
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            $this->errorMessage = 'Unable to complete sale right now. Please try again.';
        }
    }

    private function completeCreditSale(): void
    {
        if (trim($this->creditCustomerName) === '') {
            $this->errorMessage = 'Customer name is required for credit sales.';
            return;
        }

        try {
            $user = auth()->user();
            $summary = DB::transaction(function () use ($user) {
                $creditor = Creditor::create([
                    'account_owner_id' => $user->accountOwnerId(),
                    'user_id' => auth()->id(),
                    'customer_name' => trim($this->creditCustomerName),
                    'customer_phone' => trim($this->creditCustomerPhone) ?: null,
                    'quantity' => 0,
                    'total' => 0,
                    'status' => 'unpaid',
                    'created_at' => Carbon::now(),
                ]);

                $subtotalPrice = 0;
                $totalQty = 0;

                foreach ($this->cart as $cartItem) {
                    $product = Product::forAccount($user)
                        ->whereKey($cartItem['product_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $product) {
                        throw new \Exception("Product {$cartItem['product_id']} not found");
                    }

                    if ($product->quantity < $cartItem['quantity']) {
                        throw new \Exception(
                            "Insufficient stock for {$product->name}. Available: {$product->quantity}"
                        );
                    }

                    CreditorItem::create([
                        'creditor_id' => $creditor->id,
                        'product_id' => $product->id,
                        'quantity' => $cartItem['quantity'],
                        'price' => $product->price,
                    ]);

                    $subtotalPrice += round((float) $product->price * $cartItem['quantity'], 2);
                    $totalQty += $cartItem['quantity'];
                }

                $discountPercent = $this->effectiveDiscountPercent();
                $discountAmount = $this->calculateDiscountAmount($subtotalPrice);
                $finalTotal = round(max(0, $subtotalPrice - $discountAmount), 2);

                $discountKind = 'none';
                $discountValue = 0;
                if ($discountAmount > 0) {
                    if ($this->discountType === 'manual') {
                        $discountKind = 'amount';
                        $discountValue = $discountAmount;
                    } else {
                        $discountKind = 'percent';
                        $discountValue = $discountPercent;
                    }
                }

                $creditor->update([
                    'quantity' => $totalQty,
                    'subtotal' => round($subtotalPrice, 2),
                    'discount_type' => $discountKind,
                    'discount_value' => round($discountValue, 2),
                    'discount_amount' => round($discountAmount, 2),
                    'total' => $finalTotal,
                ]);

                return [
                    'creditor_id' => $creditor->id,
                    'total' => $finalTotal,
                    'customer' => $creditor->customer_name,
                ];
            });

            $this->lastSaleId = null;
            $this->successMessage = "✓ Credit saved for {$summary['customer']} (TZS " . number_format($summary['total'], 2) . '). Mark as paid from Creditors page.';
            $this->cart = [];
            $this->barcodeInput = '';
            $this->discountType = 'none';
            $this->manualDiscountAmount = '';
            $this->paymentMode = 'cash';
            $this->creditCustomerName = '';
            $this->creditCustomerPhone = '';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('POS credit sale failed.', [
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            $this->errorMessage = 'Unable to save this credit sale right now. Please try again.';
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->barcodeInput = '';
        $this->paymentMode = 'cash';
        $this->creditCustomerName = '';
        $this->creditCustomerPhone = '';
        $this->discountType = 'none';
        $this->manualDiscountAmount = '';
    }

    public function startNewSale(): void
    {
        $this->lastSaleId = null;
        $this->cart = [];
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->barcodeInput = '';
        $this->paymentMode = 'cash';
        $this->creditCustomerName = '';
        $this->creditCustomerPhone = '';
        $this->discountType = 'none';
        $this->manualDiscountAmount = '';
    }

    public function render()
    {
        $quickAddProducts = Product::forAccount(auth()->user())
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $cartProducts = [];
        if (!empty($this->cart)) {
            $cartProducts = Product::forAccount(auth()->user())
                ->whereIn('id', array_column($this->cart, 'product_id'))
                ->get();
        }

        return view('livewire.pos', [
            'cart' => $this->cart,
            'quickAddProducts' => $quickAddProducts,
            'cartProducts' => $cartProducts,
            'cartSubtotal' => $this->cartSubtotal,
            'discountPercent' => $this->discountPercent,
            'discountAmount' => $this->discountAmount,
            'cartTotal' => $this->cartTotal,
            'cartItemsCount' => $this->cartItemsCount,
            'lastSaleId' => $this->lastSaleId,
        ])->layout('layouts.app', ['title' => 'Point of Sale', 'active' => 'pos']);
    }

    private function effectiveDiscountPercent(): float
    {
        if (empty($this->cart)) {
            return 0;
        }

        return match ($this->discountType) {
            '5' => 5.0,
            '10' => 10.0,
            'manual' => 0.0,
            default => 0.0,
        };
    }

    private function calculateDiscountAmount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        if ($this->discountType === 'manual') {
            return round(min($subtotal, max(0, (float) $this->manualDiscountAmount)), 2);
        }

        return round(($subtotal * $this->effectiveDiscountPercent()) / 100, 2);
    }
}
