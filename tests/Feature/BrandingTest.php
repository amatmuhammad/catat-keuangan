<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_the_saku_kas_logo_and_brand_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('assets/images/brand/logo/saku-kas-icon.png', false)
            ->assertSee('Saku', false)
            ->assertSee('brand-name-accent', false)
            ->assertSee('Kas', false);
    }

    public function test_dashboard_chart_uses_the_brand_palette(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee("colors: ['#1a6be0', '#f5a623']", false)
            ->assertSee('background:#1a6be0', false)
            ->assertSee('background:#f5a623', false);
    }

    public function test_dashboard_cards_use_branded_style_classes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('stat-card-personal', false)
            ->assertSee('dashboard-card', false)
            ->assertSee('balance-card', false)
            ->assertSee('stat-card-income', false)
            ->assertSee('stat-card-expense', false);
    }

    public function test_brand_logo_icon_exists_on_disk(): void
    {
        $this->assertFileExists(public_path('assets/images/brand/logo/saku-kas-icon.png'));
        $this->assertFileExists(public_path('assets/images/brand/logo/saku-kas-logo.png'));
    }
}
