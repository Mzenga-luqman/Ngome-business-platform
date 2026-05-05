<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 20px;
        }
        .receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .shop-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .shop-subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        .receipt-num {
            font-size: 11px;
            margin-top: 5px;
            color: #666;
        }
        .date-time {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
            border-bottom: 1px dotted #999;
            padding-bottom: 8px;
        }
        .items-section {
            margin: 10px 0;
            border-bottom: 1px dashed #999;
            padding-bottom: 10px;
        }
        .item-header {
            display: grid;
            grid-template-columns: 1fr 60px 60px;
            gap: 5px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .item-row {
            display: grid;
            grid-template-columns: 1fr 60px 60px;
            gap: 5px;
            font-size: 11px;
            margin-bottom: 4px;
            line-height: 1.2;
            word-wrap: break-word;
            word-break: break-word;
        }
        .item-name {
            word-break: break-word;
        }
        .item-qty, .item-price {
            text-align: right;
        }
        .totals {
            margin: 10px 0;
            border-top: 1px dashed #999;
            padding-top: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .grand-total {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 10px;
            border-top: 1px dotted #999;
            padding-top: 8px;
        }
        .thank-you {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt { width: 100%; margin: 0; box-shadow: none; border: none; }
            button { display: none; }
        }
        .print-button {
            display: block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .print-button:hover {
            background: #1d4ed8;
        }
        .back-button {
            display: block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .back-button:hover {
            background: #059669;
        }
    </style>
</head>
<body>

<div class="receipt">
    <div class="header">
        <div class="shop-name">NGOME SHOP</div>
        <div class="shop-subtitle">Ngome Technology</div>
        <div class="receipt-num">Receipt #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div class="date-time">{{ $sale->sold_at->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="items-section">
        <div class="item-header">
            <div>PRODUCT</div>
            <div>QTY</div>
            <div>TOTAL</div>
        </div>
        @forelse($sale->items as $item)
        <div class="item-row">
            <div class="item-name">{{ $item->product->name }}</div>
            <div class="item-qty">{{ $item->quantity }}</div>
            <div class="item-price">{{ number_format($item->quantity * $item->price, 2) }}</div>
        </div>
        @empty
        <div style="text-align: center; font-size: 10px; color: #999; padding: 10px 0;">
            No items found
        </div>
        @endforelse
    </div>

    <div class="totals">
        @php
            $subtotal = $sale->subtotal ?? $sale->total;
            $discountAmount = (float) ($sale->discount_amount ?? 0);
        @endphp
        <div class="total-row">
            <span>Items:</span>
            <span>{{ $sale->quantity }}</span>
        </div>
        <div class="total-row">
            <span>Subtotal:</span>
            <span>TZS {{ number_format($subtotal, 2) }}</span>
        </div>
        @if($discountAmount > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>- TZS {{ number_format($discountAmount, 2) }}</span>
        </div>
        @endif
        <div class="grand-total">
            <span>TOTAL:</span>
            <span>TZS {{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <div class="thank-you">Thank You!</div>
        <div style="margin-top: 5px; font-size: 9px;">
            Please come again
        </div>
    </div>
</div>

<button class="print-button" onclick="window.print()">🖨️ Print Receipt</button>
<button class="back-button" onclick="window.close()">← Back to Dashboard</button>

</body>
</html>
