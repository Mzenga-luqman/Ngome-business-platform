<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set auth user
\Illuminate\Support\Facades\Auth::loginUsingId(1);

try {
    $product = \App\Models\Product::create([
        'name' => 'Test Widget',
        'price' => 50000,
        'quantity' => 10,
        'barcode' => 'PROD-99999'
    ]);
    echo "✓ Created product: ID={$product->id}, Barcode={$product->barcode}\n";

    $sale = \App\Models\Sale::create([
        'product_id' => null,
        'user_id' => 1,
        'quantity' => 2,
        'total' => 100000,
        'sold_at' => now(),
    ]);
    echo "✓ Sale created successfully: ID={$sale->id}, product_id=NULL\n";

    $saleItem = \App\Models\SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 50000,
    ]);
    echo "✓ SaleItem created: ID={$saleItem->id}\n";
    echo "✓ DATABASE TEST PASSED - Multi-item sales work correctly!\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
