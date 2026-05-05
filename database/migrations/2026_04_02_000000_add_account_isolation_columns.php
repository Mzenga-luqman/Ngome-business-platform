<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('account_owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->boolean('is_worker')->default(false)->after('is_admin');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('account_owner_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('account_owner_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('account_owner_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });

        DB::table('users')->whereNull('account_owner_id')->update([
            'account_owner_id' => DB::raw('id'),
        ]);

        $defaultOwnerId = DB::table('users')
            ->where('is_admin', false)
            ->orderBy('id')
            ->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        if (! $defaultOwnerId) {
            return;
        }

        DB::table('products')->whereNull('account_owner_id')->update([
            'account_owner_id' => $defaultOwnerId,
        ]);

        DB::table('expenses')->whereNull('account_owner_id')->update([
            'account_owner_id' => $defaultOwnerId,
        ]);

        $users = DB::table('users')
            ->select('id', 'account_owner_id')
            ->get()
            ->mapWithKeys(fn ($user) => [$user->id => $user->account_owner_id ?: $user->id]);

        DB::table('sales')
            ->select('id', 'user_id')
            ->orderBy('id')
            ->chunkById(100, function ($sales) use ($users, $defaultOwnerId) {
                foreach ($sales as $sale) {
                    DB::table('sales')
                        ->where('id', $sale->id)
                        ->update([
                            'account_owner_id' => $sale->user_id
                                ? ($users[$sale->user_id] ?? $defaultOwnerId)
                                : $defaultOwnerId,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_owner_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_owner_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_owner_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_owner_id');
            $table->dropColumn('is_worker');
        });
    }
};