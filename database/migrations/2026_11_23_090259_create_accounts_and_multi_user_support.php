<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('account_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['account_id', 'user_id']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_account_id')
                ->nullable()
                ->after('remember_token')
                ->constrained('accounts')
                ->nullOnDelete();
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index(['account_id', 'transaction_date']);
        });

        $this->backfillPersonalAccounts();

        // Index user_id tetap dibutuhkan foreign key setelah unique lama dihapus.
        Schema::table('wallets', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique('wallets_user_id_name_unique');
            $table->unique(['account_id', 'name']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique('categories_user_id_name_type_unique');
            $table->unique(['account_id', 'name', 'type']);
        });
    }

    /**
     * Setiap pengguna lama mendapat akun pribadi agar data yang sudah ada tetap utuh.
     */
    private function backfillPersonalAccounts(): void
    {
        DB::table('users')
            ->select(['id', 'name', 'email'])
            ->orderBy('id')
            ->chunkById(200, function ($users): void {
                foreach ($users as $user) {
                    $accountName = 'Akun '.$user->name;
                    $now = now();

                    $accountId = DB::table('accounts')->insertGetId([
                        'name' => $accountName,
                        'owner_id' => $user->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('account_user')->insert([
                        'account_id' => $accountId,
                        'user_id' => $user->id,
                        'role' => 'owner',
                        'joined_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['current_account_id' => $accountId]);

                    DB::table('wallets')
                        ->where('user_id', $user->id)
                        ->update(['account_id' => $accountId]);

                    DB::table('categories')
                        ->where('user_id', $user->id)
                        ->update(['account_id' => $accountId]);

                    DB::table('transactions')
                        ->where('user_id', $user->id)
                        ->update(['account_id' => $accountId]);

                    DB::table('activity_logs')->insert([
                        'account_id' => $accountId,
                        'user_id' => $user->id,
                        'action' => 'account.created',
                        'description' => 'Akun "'.$accountName.'" dibuat.',
                        'properties' => json_encode(['account_name' => $accountName], JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique(['account_id', 'name']);
            $table->unique(['user_id', 'name']);
            $table->dropIndex(['user_id']);
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['account_id', 'name', 'type']);
            $table->unique(['user_id', 'name', 'type']);
            $table->dropIndex(['user_id']);
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'transaction_date']);
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_account_id');
        });

        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('account_user');
        Schema::dropIfExists('accounts');
    }
};
