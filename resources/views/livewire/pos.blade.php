<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-lime-100">

    {{-- TOP ALERTS --}}
    @if($successMessage)
    <div class="fixed top-4 right-4 z-50 max-w-md rounded-xl border border-emerald-200 bg-emerald-500 px-5 py-3 text-white shadow-xl">
        <p class="font-semibold">{{ $successMessage }}</p>
    </div>
    @endif

    @if($errorMessage)
    <div class="fixed top-4 left-4 z-50 max-w-md rounded-xl border border-red-200 bg-red-500 px-5 py-3 text-white shadow-xl">
        <p class="font-semibold">{{ $errorMessage }}</p>
    </div>
    @endif

    <div class="mx-auto max-w-7xl px-4 py-6 pb-24 sm:px-6 lg:px-8 lg:pb-6">

        {{-- PAGE HEADER --}}
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900">{{ __('Point of Sale') }}</h1>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Scan barcode, add products, review cart, and complete sale.') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Cart Items') }}</p>
                        <p class="mt-1 text-xl font-black text-slate-900">{{ $cartItemsCount }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total') }}</p>
                        <p class="mt-1 text-xl font-black text-blue-700">TZS {{ number_format($cartTotal, 0) }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Products') }}</p>
                        <p class="mt-1 text-xl font-black text-slate-900">{{ $quickAddProducts->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($lastSaleId)
        <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-50 p-5 shadow-md">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-base font-black uppercase tracking-wide text-emerald-700">{{ __('Sale Recorded Successfully') }}</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-800">
                        {{ $successMessage ?: __('Sale has been saved. You can print the receipt now.') }}
                    </p>
                    <p class="mt-1 text-xs text-emerald-600">{{ __('Sale ID') }}: <span class="font-mono font-bold">{{ $lastSaleId }}</span></p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <a href="{{ route('receipt', ['saleId' => $lastSaleId]) }}"
                       target="_blank"
                       onclick="window.open(this.href, 'receipt', 'width=400,height=600'); return false;"
                       class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-black text-white shadow hover:bg-orange-700 transition">
                      <b>  {{ __('Print Receipt') }} </b>
                    </a>
                    <button wire:click="startNewSale"
                           class="inline-flex items-center justify-center rounded-xl border-2 border-emerald-600 bg-white px-5 py-3 text-sm font-black text-emerald-600 hover:bg-emerald-50 transition">
                        {{ __('New Sale') }}
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- MAIN LAYOUT --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- LEFT SIDE --}}
            <div class="space-y-6 lg:col-span-8">

                {{-- BARCODE PANEL --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-900">{{ __('Barcode Scanner') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Keep cursor in input box and scan, or type barcode then press Enter.') }}</p>

                    <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">{{ __('Barcode Input') }}</label>
                        <input
                            type="text"
                            id="pos-barcode-input"
                            wire:model.live.debounce.200ms="barcodeInput"
                            placeholder="{{ __('Scan barcode or type manually...') }}"
                            autocomplete="off"
                            autofocus
                            class="w-full rounded-xl border-2 border-blue-500 ring-4 ring-blue-200 px-4 py-4 text-xl font-semibold text-slate-900 outline-none bg-white transition-all duration-200"
                        />
                        <p class="text-xs text-blue-600 font-semibold mt-2">{{ __('Scanner ready - keep cursor here and scan') }}</p>
                    </div>

                    @if($lastScannedProduct)
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <p class="text-sm font-semibold text-emerald-700">{{ __('Last scanned') }}: <span class="font-black">{{ $lastScannedProduct }}</span></p>
                    </div>
                    @endif
                </section>

                {{-- QUICK ADD PANEL --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">{{ __('Quick Add Products') }}</h2>
                            <p class="text-sm text-slate-600">{{ __('Tap any product to add instantly to cart.') }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ __(':count available', ['count' => $quickAddProducts->count()]) }}</span>
                    </div>

                    <div class="grid max-h-[420px] grid-cols-1 gap-3 overflow-y-auto pr-1 sm:grid-cols-2">
                        @forelse($quickAddProducts as $product)
                        <button
                            wire:click="addProductByBarcode('{{ $product->barcode ?? $product->id }}')"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-11 h-11 rounded-lg object-cover flex-shrink-0 sm:w-8 sm:h-8">
                                <p class="truncate text-base font-black text-slate-900 sm:text-sm">{{ $product->name }}</p>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $product->barcode ?: __('No barcode') }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-sm font-black text-blue-700">TZS {{ number_format($product->price, 2) }}</p>
                                <span class="rounded bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">{{ __(':count left', ['count' => $product->quantity]) }}</span>
                            </div>
                        </button>
                        @empty
                        <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                            {{ __('No in-stock products found.') }}
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- RIGHT SIDE: CART + CHECKOUT --}}
            <aside class="space-y-6 lg:col-span-4">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-xl font-black text-slate-900">{{ __('Cart') }}</h2>
                        <p class="text-sm text-slate-600">{{ __(':count item(s)', ['count' => $cartItemsCount]) }}</p>
                    </div>

                    <div class="overflow-visible">
                        @if(empty($cart))
                        <div class="px-6 py-12 text-center">
                            <p class="font-semibold text-slate-500">{{ __('Cart is empty') }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ __('Scan or quick add products to start.') }}</p>
                        </div>
                        @else
                        <div class="divide-y divide-slate-100">
                            @foreach($cart as $cartItem)
                            @php
                                $product = $cartProducts->firstWhere('id', $cartItem['product_id']);
                            @endphp
                            @if($product)
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">TZS {{ number_format($product->price, 2) }} {{ __('each') }}</p>
                                    </div>
                                    <button wire:click="removeCartItem({{ $product->id }})" class="text-sm font-bold text-red-600 hover:text-red-700">
                                        {{ __('Remove') }}
                                    </button>
                                </div>

                                <div class="mt-3 flex items-center gap-2">
                                    <button
                                        wire:click="updateCartItemQuantity({{ $product->id }}, {{ $cartItem['quantity'] - 1 }})"
                                        class="h-9 w-9 rounded-lg border border-slate-300 bg-slate-100 text-base font-black text-slate-700 hover:bg-slate-200"
                                    >−</button>
                                    <input
                                        type="number"
                                        wire:change="updateCartItemQuantity({{ $product->id }}, $event.target.value)"
                                        value="{{ $cartItem['quantity'] }}"
                                        class="h-9 w-14 rounded-lg border border-slate-300 text-center text-sm font-black text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                        min="1"
                                        max="{{ $product->quantity }}"
                                    />
                                    <button
                                        wire:click="updateCartItemQuantity({{ $product->id }}, {{ $cartItem['quantity'] + 1 }})"
                                        class="h-9 w-9 rounded-lg border border-slate-300 bg-slate-100 text-base font-black text-slate-700 hover:bg-slate-200"
                                    >+</button>
                                    <span class="ml-auto text-sm font-black text-blue-700">
                                        TZS {{ number_format($product->price * $cartItem['quantity'], 2) }}
                                    </span>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">{{ __('Payment Type') }}</p>
                        <select wire:model.live="paymentMode"
                                class="mt-3 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="credit">{{ __('Credit') }}</option>
                        </select>

                        @if($paymentMode === 'credit')
                            <div class="mt-3 space-y-2">
                                <input type="text"
                                       wire:model.live.debounce.300ms="creditCustomerName"
                                       placeholder="{{ __('Customer name (required)') }}"
                                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200" />
                                <input type="text"
                                       wire:model.live.debounce.300ms="creditCustomerPhone"
                                       placeholder="{{ __('Customer phone (optional)') }}"
                                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200" />
                                <p class="text-xs text-amber-700 font-semibold">
                                    {{ __('Credit sales are not deducted from stock and not counted as sales until marked paid in Creditors.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">{{ __('Discount') }}</p>

                        <div class="mt-3 grid grid-cols-4 gap-2">
                            <button wire:click="setDiscountType('none')"
                                    class="rounded-lg px-2 py-2 text-xs font-bold transition {{ $discountType === 'none' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                {{ __('None') }}
                            </button>
                            <button wire:click="setDiscountType('5')"
                                    class="rounded-lg px-2 py-2 text-xs font-bold transition {{ $discountType === '5' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                5%
                            </button>
                            <button wire:click="setDiscountType('10')"
                                    class="rounded-lg px-2 py-2 text-xs font-bold transition {{ $discountType === '10' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                10%
                            </button>
                            <button wire:click="setDiscountType('manual')"
                                    class="rounded-lg px-2 py-2 text-xs font-bold transition {{ $discountType === 'manual' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                {{ __('Manual') }}
                            </button>
                        </div>

                        @if($discountType === 'manual')
                        <div class="mt-3">
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600">{{ __('Manual Discount (TZS)') }}</label>
                            <input type="number"
                                   min="0"
                                   step="0.01"
                                wire:model.live.debounce.300ms="manualDiscountAmount"
                                   placeholder="0"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                        </div>
                        @endif

                        <div class="mt-3 space-y-1 text-xs text-slate-600">
                            <div class="flex items-center justify-between">
                                <span>{{ __('Subtotal') }}</span>
                                <span class="font-bold">TZS {{ number_format($cartSubtotal, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>
                                    {{ __('Discount') }}
                                    @if($discountType === 'manual')
                                        ({{ __('Manual TZS') }})
                                    @else
                                        ({{ number_format($discountPercent, 2) }}%)
                                    @endif
                                </span>
                                <span class="font-bold text-red-600">- TZS {{ number_format($discountAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-blue-600 px-5 py-5 text-white">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">{{ __('Total Amount') }}</p>
                        <p class="mt-2 text-4xl font-black">TZS {{ number_format($cartTotal, 2) }}</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        @if(!empty($cart))
                        <button
                            wire:click="completeSale"
                            class="w-full rounded-xl border-2 {{ $paymentMode === 'credit' ? 'border-amber-600 text-amber-700 hover:bg-amber-50' : 'border-green-600 text-green-700 hover:bg-green-50' }} bg-white px-5 py-4 text-lg font-black shadow"
                        >
                            {{ $paymentMode === 'credit' ? __('SAVE AS CREDIT') : __('COMPLETE SALE') }}
                        </button>
                        <button
                            wire:click="clearCart"
                            class="w-full rounded-xl bg-red-500 px-5 py-3 text-base font-bold text-white hover:bg-red-600"
                        >
                            {{ __('Clear Cart') }}
                        </button>
                        @else
                        <div class="w-full rounded-xl bg-slate-200 px-5 py-4 text-center text-base font-bold text-green-700">
                            {{ __('Complete Sale') }}
                        </div>
                        @endif

                        @if($lastSaleId)
                        <div class="mt-4 space-y-2 rounded-xl border-2 border-emerald-300 bg-emerald-50 p-4">
                            <a href="{{ route('receipt', ['saleId' => $lastSaleId]) }}"
                               target="_blank"
                               onclick="window.open(this.href, 'receipt', 'width=400,height=600'); return false;"
                               class="flex w-full items-center justify-center rounded-lg bg-orange-600 px-4 py-3 text-sm font-black text-white hover:bg-orange-700 transition">
                                {{ __('PRINT RECEIPT') }}
                            </a>
                            <button wire:click="startNewSale"
                                   class="w-full rounded-lg border border-emerald-400 bg-white px-4 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-50">
                                {{ __('Start New Sale') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>

    {{-- MOBILE STICKY CHECKOUT BAR (hidden on lg+) --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden border-t border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center gap-3 px-4 py-3">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-500">{{ __(':count item(s) in cart', ['count' => $cartItemsCount]) }}</p>
                <p class="text-xl font-black text-blue-700">TZS {{ number_format($cartTotal, 2) }}</p>
            </div>
            @if(!empty($cart))
                <button wire:click="completeSale"
                        class="flex-shrink-0 rounded-xl {{ $paymentMode === 'credit' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600' }} px-5 py-3 text-sm font-black text-white shadow-lg active:scale-95 transition-transform">
                    {{ $paymentMode === 'credit' ? __('SAVE CREDIT') : __('COMPLETE SALE') }}
                </button>
            @else
                <div class="flex-shrink-0 rounded-xl bg-slate-200 px-5 py-3 text-sm font-bold text-slate-400">
                    {{ __('Cart Empty') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('barcode-scan-complete', function () {
        var input = document.getElementById('pos-barcode-input');
        if (input) {
            input.focus();
        }
    });
});
</script>

