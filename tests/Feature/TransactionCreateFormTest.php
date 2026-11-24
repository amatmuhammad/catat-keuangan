<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCreateFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_jenis_transaksi_memiliki_radio_dan_label_yang_bisa_diklik(): void
    {
        $user = User::factory()->create();
        Wallet::factory()->for($user)->create([
            'account_id' => $user->currentAccount->id,
            'is_shared' => false,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.create'));

        $response->assertOk();

        // Radio input harus ada dengan id yang sesuai.
        $response->assertSee('id="type-income"', false);
        $response->assertSee('id="type-expense"', false);

        // Label harus menjadi sibling langsung dan terhubung via atribut for.
        $response->assertSee('for="type-income"', false);
        $response->assertSee('for="type-expense"', false);

        // Kartu harus berupa label (bukan div) agar seluruh area bisa diklik.
        $response->assertSee('<label class="choice-card choice-income" for="type-income"', false);
        $response->assertSee('<label class="choice-card choice-expense" for="type-expense"', false);

        // Salah satu opsi harus tercentang secara default.
        $response->assertSee('checked>', false);
    }
}
