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
        Schema::table('wallets', function (Blueprint $table) {
            $table->boolean('is_shared')->default(true)->after('balance')->index();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->boolean('is_shared')->default(true)->after('description')->index();
        });

        // Nama dompet kini unik per jenis: bersama per akun, pribadi per anggota.
        Schema::table('wallets', function (Blueprint $table) {
            // Index account_id tetap dibutuhkan foreign key setelah unique lama dihapus.
            $table->index('account_id');
            $table->dropUnique('wallets_account_id_name_unique');
            $table->index(['account_id', 'is_shared', 'user_id']);
        });

        $this->backfillPersonalWallets();
    }

    /**
     * Setiap anggota akun mendapat satu dompet pribadi tanpa mengubah saldo bersama.
     */
    private function backfillPersonalWallets(): void
    {
        DB::table('account_user')
            ->select(['id', 'account_id', 'user_id'])
            ->orderBy('id')
            ->chunkById(200, function ($memberships): void {
                foreach ($memberships as $membership) {
                    $exists = DB::table('wallets')
                        ->where('account_id', $membership->account_id)
                        ->where('user_id', $membership->user_id)
                        ->where('is_shared', false)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $now = now();

                    DB::table('wallets')->insert([
                        'account_id' => $membership->account_id,
                        'user_id' => $membership->user_id,
                        'name' => 'Saldo Pribadi',
                        'balance' => 0,
                        'is_shared' => false,
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
            $table->dropIndex(['account_id', 'is_shared', 'user_id']);
            $table->unique(['account_id', 'name']);
            $table->dropIndex(['account_id']);
            $table->dropColumn('is_shared');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['is_shared']);
            $table->dropColumn('is_shared');
        });
    }
};
