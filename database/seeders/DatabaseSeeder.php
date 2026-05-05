<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Subscription::updateOrCreate(
            ['name' => 'Basic'],
            ['price' => 20000, 'duration_months' => 1]
        );

        Subscription::updateOrCreate(
            ['name' => 'Standard'],
            ['price' => 50000, 'duration_months' => 3]
        );

        Subscription::updateOrCreate(
            ['name' => 'Premium'],
            ['price' => 80000, 'duration_months' => 6]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'account_owner_id' => null,
                'is_admin' => true,
                'is_worker' => false,
                'subscription_id' => null,
                'subscription_expiry' => null,
            ]
        );

        $admin->forceFill(['account_owner_id' => $admin->id])->save();
    }
}
