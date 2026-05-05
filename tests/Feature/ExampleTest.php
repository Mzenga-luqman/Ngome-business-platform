<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_access_dashboard(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 30000,
            'duration_months' => 1,
        ]);

        $user = User::factory()->create([
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(15),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_registration_redirects_new_user_to_subscription_plans(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'New Shop Owner',
            'email' => 'new-owner@example.com',
            'password' => 'strong-pass-123',
            'password_confirmation' => 'strong-pass-123',
        ]);

        $response->assertRedirect(route('subscription.plans'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-owner@example.com',
            'is_admin' => false,
            'is_worker' => false,
        ]);

        $user = User::where('email', 'new-owner@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame($user->id, $user->account_owner_id);
    }

    public function test_subscribed_user_login_redirects_to_dashboard(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 30000,
            'duration_months' => 1,
        ]);

        $user = User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('password123'),
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(15),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $user->forceFill(['account_owner_id' => $user->id])->save();

        $response = $this->post(route('login.store'), [
            'email' => 'active@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_subscribed_user_can_login_with_username(): void
    {
        $plan = Subscription::create([
            'name' => 'Basic',
            'price' => 30000,
            'duration_months' => 1,
        ]);

        $user = User::factory()->create([
            'username' => 'shopowner1',
            'email' => 'owner1@example.com',
            'password' => Hash::make('password123'),
            'subscription_id' => $plan->id,
            'subscription_expiry' => now()->addDays(15),
            'is_admin' => false,
            'is_worker' => false,
        ]);
        $user->forceFill(['account_owner_id' => $user->id])->save();

        $response = $this->post(route('login.store'), [
            'login' => 'shopowner1',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
    }
}
