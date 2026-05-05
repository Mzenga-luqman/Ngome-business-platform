<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaymentsPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panel_shows_approved_payment_history_with_reference(): void
    {
        $plan = Subscription::create([
            'name' => 'Starter',
            'price' => 25000,
            'duration_months' => 1,
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_worker' => false,
        ]);

        $owner = User::factory()->create([
            'is_admin' => false,
            'is_worker' => false,
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addMonth(),
        ]);
        $owner->forceFill(['account_owner_id' => $owner->id])->save();

        $invoice = Invoice::create([
            'user_id' => $owner->id,
            'subscription_id' => $plan->id,
            'invoice_code' => 'NGOME-TEST-0001',
            'amount' => 25000,
            'status' => Invoice::STATUS_APPROVED,
        ]);

        Payment::create([
            'user_id' => $owner->id,
            'subscription_id' => $plan->id,
            'invoice_id' => $invoice->id,
            'provider' => 'mpesa',
            'phone_number' => '0712345678',
            'payment_reference' => 'REF-APPROVED-123',
            'status' => Payment::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertSee('Approved Payment History')
            ->assertSee('REF-APPROVED-123')
            ->assertSee('NGOME-TEST-0001');
    }

    public function test_admin_panel_shows_all_users_list(): void
    {
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'is_worker' => false,
        ]);

        $owner = User::factory()->create([
            'name' => 'Shop Owner',
            'email' => 'owner@example.com',
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $owner->forceFill(['account_owner_id' => $owner->id])->save();

        $worker = User::factory()->create([
            'name' => 'Shop Worker',
            'email' => 'worker@example.com',
            'is_admin' => false,
            'is_worker' => true,
            'account_owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.payments'));

        $response->assertOk();
        $response->assertSee('All Users');
        $response->assertSee('admin@example.com');
        $response->assertSee('owner@example.com');
        $response->assertSee('worker@example.com');
    }
}
