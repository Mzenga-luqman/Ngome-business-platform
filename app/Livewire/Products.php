<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Products extends Component
{
    use WithFileUploads;

    public string $filter = 'all';
    public string $search = '';
    public string  $name     = '';
    public string  $price    = '';
    public $quantity = 0;
    public string  $barcode  = '';
    public ?int    $editId   = null;
    public $image = null;
    public ?string $currentImagePath = null;
    public int $imageMaxMb = 10;
    public bool $imageUploadAttempted = false;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:200',
            'price'    => 'required|numeric|min:0|max:9999999.99',
            'quantity' => 'required|integer|min:0|max:1000000',
            'barcode'  => 'nullable|string|max:100|unique:products,barcode' . ($this->editId ? ",{$this->editId}" : ''),
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . $this->imageMaxKb(),
        ];
    }

    protected function messages(): array
    {
        return [
            'image.max' => 'The product image must not be larger than ' . $this->imageMaxMb . ' MB.',
            'image.mimes' => 'The product image must be a JPG, JPEG, PNG, or WEBP file.',
            'image.image' => 'Please upload a valid image file.',
        ];
    }

    public function mount(): void
    {
        $filter = request()->query('filter', 'all');
        $this->filter = in_array($filter, ['all', 'low-stock'], true) ? $filter : 'all';
        $this->search = trim((string) request()->query('q', ''));
        $this->imageMaxMb = max(1, (int) config('uploads.product_image_max_mb', 10));
    }

    public function saveProduct(): void
    {
        if ($this->imageUploadAttempted && ! $this->image) {
            $this->addError('image', 'Image failed to upload. Please re-select the image and try again.');
            return;
        }

        if ($this->getErrorBag()->has('image')) {
            return;
        }

        $this->validate();

        $user = auth()->user();

        $product = $this->editId ? Product::forAccount($user)->findOrFail($this->editId) : null;
        $newImagePath = null;
        $oldImagePath = $product?->hasManagedImage() ? $product->image_path : null;

        $data = [
            'account_owner_id' => $user->accountOwnerId(),
            'name'     => $this->sanitizeName($this->name),
            'price'    => round((float) $this->price, 2),
            'quantity' => (int) $this->quantity,
            'barcode'  => !empty($this->barcode) ? trim($this->barcode) : null,
        ];

        if ($this->image) {
            $newImagePath = $this->image->store('products', 'public');
            $data['image_path'] = $newImagePath;
        }

        try {
            DB::transaction(function () use ($product, $data) {
                if ($this->editId) {
                    $product->update($data);
                    return;
                }

                $newProduct = Product::create($data);
                // Auto-generate barcode if not set
                if (!$newProduct->barcode) {
                    $newProduct->update(['barcode' => 'PROD-' . str_pad($newProduct->id, 5, '0', STR_PAD_LEFT)]);
                }
            });
        } catch (\Throwable $e) {
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $e;
        }

        if ($oldImagePath && $newImagePath && $oldImagePath !== $newImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        session()->flash('success', $this->editId
            ? 'Product updated successfully!'
            : 'Product added successfully!');

        $this->resetForm();
    }

    public function updatedImage(): void
    {
        try {
            $this->validateOnly('image');
        } catch (ValidationException $e) {
            $this->image = null;
            throw $e;
        }
    }

    public function updatingImage(): void
    {
        $this->imageUploadAttempted = true;
        $this->resetValidation('image');
    }

    public function editProduct(int $id): void
    {
        $product        = Product::forAccount(auth()->user())->findOrFail($id);
        $this->editId   = $id;
        $this->name     = $product->name;
        $this->price    = (string) $product->price;
        $this->quantity = $product->quantity;
        $this->barcode  = $product->barcode ?? '';
        $this->currentImagePath = $product->image_path;
        $this->image = null;
        $this->imageUploadAttempted = false;
    }

    public function deleteProduct(int $id): void
    {
        $product = Product::forAccount(auth()->user())->findOrFail($id);
        $imagePath = $product->hasManagedImage() ? $product->image_path : null;

        DB::transaction(fn () => $product->delete());

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($this->editId === $id) {
            $this->resetForm();
        }

        session()->flash('success', 'Product deleted.');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'price', 'quantity', 'barcode', 'editId', 'image', 'currentImagePath', 'imageUploadAttempted']);
        $this->quantity = 0;
        $this->resetValidation();
    }

    private function sanitizeName(string $name): string
    {
        $name = trim(strip_tags($name));

        return preg_replace('/\s+/', ' ', $name) ?: '';
    }

    public function render()
    {
        $user = auth()->user();
        $productsQuery = Product::forAccount($user)->orderByDesc('created_at');

        if ($this->search !== '') {
            $search = trim($this->search);
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($this->filter === 'low-stock') {
            $productsQuery->where('quantity', '<=', 5);
        }

        $products = $productsQuery->get();

        $onCreditByProductId = DB::table('creditor_items')
            ->join('creditors', 'creditors.id', '=', 'creditor_items.creditor_id')
            ->where('creditors.account_owner_id', $user->accountOwnerId())
            ->where('creditors.status', 'unpaid')
            ->select('creditor_items.product_id', DB::raw('SUM(creditor_items.quantity) as qty'))
            ->groupBy('creditor_items.product_id')
            ->pluck('qty', 'creditor_items.product_id');

        $products->transform(function ($product) use ($onCreditByProductId) {
            $onCredit = (int) ($onCreditByProductId[$product->id] ?? 0);
            $available = max(0, (int) $product->quantity - $onCredit);

            $product->on_credit_quantity = $onCredit;
            $product->available_quantity = $available;

            return $product;
        });

        return view('livewire.products', [
            'products' => $products,
        ])->layout('layouts.app', ['title' => 'Products', 'active' => 'products']);
    }

    private function imageMaxKb(): int
    {
        return $this->imageMaxMb * 1024;
    }
}
