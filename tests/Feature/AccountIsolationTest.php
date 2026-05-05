<?php

namespace Tests\Feature;

use App\Livewire\Pos;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_accounts_inherit_the_main_account_subscription(): void
    {
        $plan = Subscription::create([
            'name' => 'Standard',
            'price' => 50000,
            'duration_months' => 3,
        ]);

        $owner = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addMonth(),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $owner->forceFill(['account_owner_id' => $owner->id])->save();

        $worker = User::factory()->create([
            'account_owner_id' => $owner->id,
            'is_worker' => true,
            'is_admin' => false,
            'subscription_id' => null,
            'subscription_expiry' => null,
        ]);

        $this->assertTrue($worker->fresh()->hasActiveSubscription());
        $this->assertSame(3, $owner->fresh()->workerLimit());

        $response = $this->actingAs($worker)->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_worker_login_redirects_to_subscription_plans_when_owner_subscription_is_expired(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 20000,
            'duration_months' => 1,
        ]);

        $owner = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->subDay(),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $owner->forceFill(['account_owner_id' => $owner->id])->save();

        $worker = User::factory()->create([
            'email' => 'expired-worker-login@example.com',
            'password' => 'password',
            'account_owner_id' => $owner->id,
            'is_worker' => true,
            'is_admin' => false,
            'subscription_id' => null,
            'subscription_expiry' => null,
        ]);

        $this->assertFalse($worker->fresh()->hasActiveSubscription());

        $this->post(route('login.store'), [
            'email' => 'expired-worker-login@example.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('subscription.plans'));
    }

    public function test_sales_and_receipts_are_scoped_to_the_logged_in_account(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 20000,
            'duration_months' => 1,
        ]);

        $ownerA = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerA->forceFill(['account_owner_id' => $ownerA->id])->save();

        $ownerB = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerB->forceFill(['account_owner_id' => $ownerB->id])->save();

        $productA = Product::create([
            'account_owner_id' => $ownerA->id,
            'name' => 'Alpha Product',
            'barcode' => 'ALPHA-001',
            'price' => 1200,
            'quantity' => 20,
        ]);

        $productB = Product::create([
            'account_owner_id' => $ownerB->id,
            'name' => 'Beta Product',
            'barcode' => 'BETA-001',
            'price' => 9900,
            'quantity' => 20,
        ]);

        $saleA = Sale::create([
            'account_owner_id' => $ownerA->id,
            'product_id' => null,
            'user_id' => $ownerA->id,
            'quantity' => 2,
            'total' => 2400,
            'sold_at' => now(),
        ]);

        SaleItem::create([
            'sale_id' => $saleA->id,
            'product_id' => $productA->id,
            'quantity' => 2,
            'price' => 1200,
        ]);

        $saleB = Sale::create([
            'account_owner_id' => $ownerB->id,
            'product_id' => null,
            'user_id' => $ownerB->id,
            'quantity' => 1,
            'total' => 9900,
            'sold_at' => now(),
        ]);

        SaleItem::create([
            'sale_id' => $saleB->id,
            'product_id' => $productB->id,
            'quantity' => 1,
            'price' => 9900,
        ]);

        $salesResponse = $this->actingAs($ownerA)->get(route('sales'));

        $salesResponse
            ->assertOk()
            ->assertSee('Alpha Product')
            ->assertDontSee('Beta Product');

        $this->actingAs($ownerA)
            ->get(route('receipt', ['saleId' => $saleB->id]))
            ->assertNotFound();
    }

    public function test_pos_quick_add_only_shows_products_for_logged_in_account(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 20000,
            'duration_months' => 1,
        ]);

        $ownerA = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerA->forceFill(['account_owner_id' => $ownerA->id])->save();

        $ownerB = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerB->forceFill(['account_owner_id' => $ownerB->id])->save();

        Product::create([
            'account_owner_id' => $ownerA->id,
            'name' => 'Owner A Product',
            'barcode' => 'A-100',
            'price' => 1500,
            'quantity' => 5,
        ]);

        Product::create([
            'account_owner_id' => $ownerB->id,
            'name' => 'Owner B Product',
            'barcode' => 'B-100',
            'price' => 2500,
            'quantity' => 5,
        ]);

        $this->actingAs($ownerA)
            ->get(route('pos'))
            ->assertOk()
            ->assertSee('Owner A Product')
            ->assertDontSee('Owner B Product');
    }

    public function test_pos_barcode_lookup_is_scoped_to_logged_in_account(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 20000,
            'duration_months' => 1,
        ]);

        $ownerA = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerA->forceFill(['account_owner_id' => $ownerA->id])->save();

        $ownerB = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(10),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $ownerB->forceFill(['account_owner_id' => $ownerB->id])->save();

        Product::create([
            'account_owner_id' => $ownerA->id,
            'name' => 'A Scoped Product',
            'barcode' => 'A-SCOPE-1',
            'price' => 1200,
            'quantity' => 4,
        ]);

        Product::create([
            'account_owner_id' => $ownerB->id,
            'name' => 'B Other Product',
            'barcode' => 'B-SCOPE-1',
            'price' => 3300,
            'quantity' => 4,
        ]);

        Livewire::actingAs($ownerA)
            ->test(Pos::class)
            ->call('addProductByBarcode', 'B-SCOPE-1')
            ->assertSet('errorMessage', 'Product not found: B-SCOPE-1')
            ->assertSet('cart', []);

        Livewire::actingAs($ownerA)
            ->test(Pos::class)
            ->call('addProductByBarcode', 'A-SCOPE-1')
            ->assertSet('errorMessage', '')
            ->assertSet('lastScannedProduct', 'A Scoped Product');
    }
}
