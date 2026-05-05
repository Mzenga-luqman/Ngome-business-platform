<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_email_and_username(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'username' => 'old_username',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'new@example.com',
            'username' => 'new_username',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'new@example.com',
            'username' => 'new_username',
        ]);
    }

    public function test_user_can_upload_profile_picture(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => 'photo_user',
            'profile_picture' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->post(route('profile.password.update'), [
            'current_password' => 'old-password-123',
            'new_password' => 'new-password-123',
            'new_password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password-123', $user->password));

        $loginWithOldPassword = $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'old-password-123',
        ]);

        $loginWithOldPassword->assertSessionHasErrors('login');

        $loginWithNewPassword = $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'new-password-123',
        ]);

        $loginWithNewPassword->assertRedirect(route('subscription.plans'));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->post(route('profile.password.update'), [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password-123',
            'new_password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('old-password-123', $user->password));
    }
}
