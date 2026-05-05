<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Products') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Manage your inventory items.') }}</p>
        </div>
        <form method="GET" action="{{ route('products') }}" class="w-full sm:max-w-md">
            <label class="sr-only" for="products-search">{{ __('Search products') }}</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        id="products-search"
                        name="q"
                        type="text"
                        value="{{ $search }}"
                        placeholder="{{ __('Search by name or barcode...') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    {{ __('Search') }}
                </button>
                @if($search !== '')
                    <a href="{{ route('products') }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        {{ __('Clear') }}
                    </a>
                @endif
            </div>
        </form>
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ $products->count() }} {{ $filter === 'low-stock' ? __('low stock products') : __('total products') }}
            </div>
            @if($filter === 'low-stock')
                <a href="{{ route('products') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ __('Clear low stock filter') }}
                </a>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3.5 shadow-sm">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button type="button" class="ml-auto text-emerald-400 hover:text-emerald-600 transition-colors" onclick="this.closest('div').remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- ===================== FORM CARD ===================== --}}
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-white font-bold flex items-center gap-2">
                @if($editId)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Edit Product') }}
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Add New Product') }}
                @endif
            </h2>
            <p class="text-blue-200 text-xs mt-0.5">{{ __('Fill in the product details below') }}</p>
        </div>

                    <form wire:submit.prevent="saveProduct" class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Name --}}
                <div class="sm:col-span-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ __('Product Name') }}</label>
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="name"
                               type="text"
                               placeholder="{{ __('e.g. Coca Cola 500ml') }}"
                               class="w-full pl-4 pr-4 py-2.5 rounded-xl border text-sm text-slate-700
                                      bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2
                                      focus:ring-blue-500 focus:border-blue-500 focus:bg-white
                                      transition-all duration-200
                                      @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror"/>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ __('Price (TZS)') }}</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">TZS</span>
                        <input wire:model.live.debounce.300ms="price"
                               type="number"
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border text-sm text-slate-700
                                      bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2
                                      focus:ring-blue-500 focus:border-blue-500 focus:bg-white
                                      transition-all duration-200
                                      @error('price') border-red-400 bg-red-50 @else border-slate-200 @enderror"/>
                    </div>
                    @error('price')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ __('Quantity') }}</label>
                    <input wire:model.live.debounce.300ms="quantity"
                           type="number"
                           min="0"
                           placeholder="0"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm text-slate-700
                                  bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-blue-500 focus:bg-white
                                  transition-all duration-200
                                  @error('quantity') border-red-400 bg-red-50 @else border-slate-200 @enderror"/>
                    @error('quantity')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Barcode / Product ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Barcode / Product ID') }}
                    </label>
                          <input wire:model.live.debounce.150ms="barcode"
                              id="barcode"
                           type="text"
                           placeholder="{{ __('Scan barcode or enter product ID') }}"
                           autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm text-slate-700
                                  bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-blue-500 focus:bg-white
                                  transition-all duration-200 font-mono
                                  @error('barcode') border-red-400 bg-red-50 @else border-slate-200 @enderror"/>
                    <p class="text-[11px] text-slate-500 mt-1">{{ __('Leave empty to auto-generate barcode from product ID.') }}</p>
                    @error('barcode')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Product Image (Optional) --}}
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ __('Product Image (Optional)') }}</label>
                    <input wire:model="image"
                           type="file"
                                    accept="image/*"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm text-slate-700
                                  bg-slate-50 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0
                                  file:bg-blue-100 file:text-blue-700 file:font-semibold
                                  hover:file:bg-blue-200 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-blue-500 focus:bg-white
                                  transition-all duration-200
                                  @error('image') border-red-400 bg-red-50 @else border-slate-200 @enderror"/>
                    <p class="text-[11px] text-slate-500 mt-1">{{ __('Accepted formats: JPG, JPEG, PNG, WEBP. Maximum file size: :size MB.', ['size' => $imageMaxMb]) }}</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-3 flex items-center gap-3">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                 class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-sm"/>
                            <span class="text-xs text-slate-500">{{ __('New image preview') }}</span>
                        @elseif($currentImagePath)
                            <img src="{{ str_starts_with($currentImagePath, 'http') ? $currentImagePath : asset('storage/' . $currentImagePath) }}" alt="Current product image"
                                 class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-sm"/>
                            <span class="text-xs text-slate-500">{{ __('Current image') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="image"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                               bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/30
                               hover:shadow-blue-700/40 hover:-translate-y-0.5 active:translate-y-0
                               transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                    @if($editId)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span wire:loading.remove wire:target="image">{{ __('Update Product') }}</span>
                        <span wire:loading wire:target="image">{{ __('Uploading image...') }}</span>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span wire:loading.remove wire:target="image">{{ __('Add Product') }}</span>
                        <span wire:loading wire:target="image">{{ __('Uploading image...') }}</span>
                    @endif
                </button>

                @if($editId)
                <button type="button"
                        wire:click="cancelEdit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                               bg-white border border-slate-200 text-slate-600 hover:bg-slate-50
                               hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ __('Cancel') }}
                </button>
                @endif
            </div>
        </form>
    </div>

    {{-- ===================== PRODUCTS TABLE ===================== --}}
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ __('All Products') }}
            </h2>
        </div>

        @if($products->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="font-semibold text-slate-600">{{ __('No products yet') }}</p>
            <p class="text-sm text-slate-400 mt-1">{{ __('Add your first product using the form above.') }}</p>
        </div>
        @else

        {{-- ===== MOBILE: card list (hidden on md+) ===== --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($products as $index => $product)
            @php
                $availableQty = (int) ($product->available_quantity ?? $product->quantity);
                $onCreditQty = (int) ($product->on_credit_quantity ?? 0);
            @endphp
            <div class="flex items-center gap-3 p-4">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0"/>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $product->name }}</p>
                    <p class="text-sm font-semibold text-slate-600 mt-0.5">TZS {{ number_format($product->price, 2) }}</p>
                    <div class="mt-1.5">
                        @if($availableQty === 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> {{ __('Out of Stock') }}
                            </span>
                        @elseif($availableQty <= 5)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> {{ __('Low') }} ({{ $availableQty }})
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ __(':count available', ['count' => $availableQty]) }}
                            </span>
                        @endif

                        @if($onCreditQty > 0)
                            <p class="text-xs font-semibold text-blue-700 mt-1">{{ __(':count on credit', ['count' => $onCreditQty]) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <button wire:click="editProduct({{ $product->id }})"
                            class="px-3 py-2 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                        {{ __('Edit') }}
                    </button>
                    <button wire:click="deleteProduct({{ $product->id }})"
                            wire:confirm="Delete '{{ $product->name }}'? This cannot be undone."
                            class="px-3 py-2 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ===== DESKTOP: full table (hidden on mobile) ===== --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Name') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Barcode') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Price') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Quantity') }}</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($products as $index => $product)
                    @php
                        $availableQty = (int) ($product->available_quantity ?? $product->quantity);
                        $onCreditQty = (int) ($product->on_credit_quantity ?? 0);
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-14 h-14 rounded-lg object-cover border border-slate-200 shadow-sm flex-shrink-0"/>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ __('Inventory item') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                {{ $product->barcode ?? __('(auto-generated)') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium">
                            TZS {{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="leading-tight">
                                <p class="font-bold {{ $availableQty <= 5 ? 'text-red-600' : 'text-slate-700' }}">
                                    {{ $availableQty }} {{ __('available') }}
                                </p>
                                @if($onCreditQty > 0)
                                    <p class="text-xs font-semibold text-blue-700">{{ $onCreditQty }} {{ __('on credit') }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($availableQty === 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    {{ __('Out of Stock') }}
                                </span>
                            @elseif($availableQty <= 5)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ __('Low Stock') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('In Stock') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="editProduct({{ $product->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                               text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('Edit') }}
                                </button>
                                <button wire:click="deleteProduct({{ $product->id }})"
                                        wire:confirm="Delete '{{ $product->name }}'? This cannot be undone."
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                               text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
