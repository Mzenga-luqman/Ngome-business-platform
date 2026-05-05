<?php

namespace Tests\Feature;

use App\Livewire\Products;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_is_saved_with_image_when_upload_succeeds(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $user->forceFill(['account_owner_id' => $user->id])->save();

        $image = UploadedFile::fake()->image('product.jpg', 600, 600)->size(512);

        Livewire::actingAs($user)
            ->test(Products::class)
            ->set('name', 'Large Product Test')
            ->set('price', '2500')
            ->set('quantity', 7)
            ->set('image', $image)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $product = Product::query()->where('name', 'Large Product Test')->first();

        $this->assertNotNull($product);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_product_is_not_saved_if_image_upload_was_attempted_but_failed(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $user->forceFill(['account_owner_id' => $user->id])->save();

        Livewire::actingAs($user)
            ->test(Products::class)
            ->set('name', 'Blocked Product')
            ->set('price', '1200')
            ->set('quantity', 4)
            ->set('imageUploadAttempted', true)
            ->call('saveProduct')
            ->assertHasErrors(['image']);

        $this->assertDatabaseMissing('products', [
            'name' => 'Blocked Product',
        ]);
    }

    public function test_failed_upload_shows_clear_error_message(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $user->forceFill(['account_owner_id' => $user->id])->save();

        Livewire::actingAs($user)
            ->test(Products::class)
            ->set('name', 'Message Check Product')
            ->set('price', '1800')
            ->set('quantity', 3)
            ->set('imageUploadAttempted', true)
            ->call('saveProduct')
            ->assertHasErrors(['image'])
            ->assertSee('Image failed to upload. Please re-select the image and try again.');

        $this->assertDatabaseMissing('products', [
            'name' => 'Message Check Product',
        ]);
    }
}
