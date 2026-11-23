<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\DefaultCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk ke');
        $this->get('/register')->assertOk()->assertSee('Buat akun baru');

        $this->get('/')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/transactions')->assertRedirect('/login');
        $this->get('/account/members')->assertRedirect('/login');
        $this->get('/account/activity')->assertRedirect('/login');
    }

    public function test_user_can_login_and_receives_a_long_lived_remember_cookie(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $response = $this->post('/login', [
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertCookie(Auth::guard('web')->getRecallerName());

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $this->post('/login', [
            'email' => 'budi@example.com',
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_creates_personal_account_with_default_wallet_and_categories(): void
    {
        $this->post('/register', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'siti@example.com')->firstOrFail();
        $account = $user->currentAccount;

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertInstanceOf(Account::class, $account);
        $this->assertSame('Akun Siti', $account->name);
        $this->assertTrue($account->isOwner($user));
        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => Account::ROLE_OWNER,
        ]);
        $this->assertDatabaseHas('wallets', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama',
        ]);
        $this->assertSame(7, $account->categories()->count());
        $this->assertDatabaseHas('categories', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Operasional',
            'type' => Category::TYPE_EXPENSE,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'action' => 'account.created',
        ]);
    }

    public function test_authenticated_user_can_open_main_pages(): void
    {
        $user = User::factory()->create();
        DefaultCategorySeeder::seedFor($user->currentAccount, $user);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Saldo Bersama')
            ->assertSee('Saldo Pribadi')
            ->assertSee($user->currentAccount->name);

        $this->actingAs($user)->get('/transactions')
            ->assertOk()
            ->assertSee('Riwayat Transaksi');

        $this->actingAs($user)->get('/transactions/create')
            ->assertOk()
            ->assertSee('Catat Transaksi')
            ->assertSee('dicatat oleh');

        $this->actingAs($user)->get('/wallets')
            ->assertOk()
            ->assertSeeText('Kas Bersama & Pribadi')
            ->assertSee('Saldo Pribadi')
            ->assertSee('Kelola Kategori');

        $this->actingAs($user)->get('/categories')
            ->assertOk()
            ->assertSee('Tambah Kategori')
            ->assertSee('Kelola Dompet');

        $this->actingAs($user)->get('/calendar')
            ->assertOk()
            ->assertSee('Kalender Arus Kas');

        $this->actingAs($user)->get('/account/members')
            ->assertOk()
            ->assertSee('Anggota Akun')
            ->assertSee('1 dari 5 slot terpakai')
            ->assertSee('1 / 5 anggota');

        $this->actingAs($user)->get('/account/activity')
            ->assertOk()
            ->assertSee('Log Aktivitas')
            ->assertSee('Akun dibuat');
    }

    public function test_user_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
