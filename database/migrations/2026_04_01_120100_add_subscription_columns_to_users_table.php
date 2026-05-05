<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('password')->constrained('subscriptions')->nullOnDelete();
            $table->timestamp('subscription_expiry')->nullable()->after('subscription_id');
            $table->boolean('is_admin')->default(false)->after('subscription_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_id');
            $table->dropColumn(['subscription_expiry', 'is_admin']);
        });
    }
};
