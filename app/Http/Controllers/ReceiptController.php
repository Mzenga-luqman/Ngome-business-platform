<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class ReceiptController extends Controller
{
    public function show($saleId)
    {
        $sale = Sale::forAccount(request()->user())
            ->with('items.product', 'user')
            ->findOrFail($saleId);

        return view('receipt', [
            'sale' => $sale,
        ]);
    }
}
