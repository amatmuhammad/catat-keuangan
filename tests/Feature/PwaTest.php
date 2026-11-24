<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_configures_a_standalone_app_that_starts_on_the_dashboard(): void
    {
        $manifest = $this->manifest();

        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/dashboard?source=pwa', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertNotEmpty($manifest['theme_color']);
        $this->assertNotEmpty($manifest['background_color']);

        $sizes = array_column($manifest['icons'], 'sizes');
        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);
    }

    public function test_pwa_icons_exist_on_disk(): void
    {
        foreach (['icon-192x192.png', 'icon-512x512.png', 'icon-maskable-512x512.png'] as $icon) {
            $this->assertFileExists(public_path('icons/'.$icon));
        }
    }

    public function test_service_worker_caches_assets_and_handles_push_events(): void
    {
        $serviceWorker = file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString("self.addEventListener('push'", $serviceWorker);
        $this->assertStringContainsString('showNotification', $serviceWorker);
        $this->assertStringContainsString("self.addEventListener('notificationclick'", $serviceWorker);
        $this->assertStringContainsString('/assets/css/cashflow.css', $serviceWorker);
        $this->assertStringContainsString('networkFirst', $serviceWorker);
    }

    public function test_dashboard_is_wired_for_pwa_install_and_push(): void
    {
        config(['webpush.vapid.public_key' => 'test-public-key']);

        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('id="pwaInstallBanner"', false)
            ->assertSee('assets/js/pwa-installer.js?v=', false)
            ->assertSee('assets/css/cashflow.css?v=', false)
            ->assertSee('name="webpush-public-key"', false)
            ->assertSee('test-public-key', false)
            ->assertSee('id="enablePushBtn"', false);
    }

    public function test_only_the_valid_manifest_is_linked(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('href="'.asset('manifest.json').'"', false)
            ->assertDontSee('assets/images/favicon/manifest.json', false)
            ->assertDontSee('android-icon-', false);

        $this->assertSame(1, substr_count($response->getContent(), 'rel="manifest"'));
    }

    public function test_install_button_and_guide_are_available_on_authenticated_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<li id="installAppNavItem">', false)
            ->assertSee('id="installAppBtn"', false)
            ->assertSee('id="installAppModal"', false)
            ->assertSee('id="installAppConfirmBtn"', false)
            ->assertSee('Pasang Catat Keuangan', false);
    }

    public function test_guests_see_the_login_page_with_pwa_meta_tags(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('assets/js/pwa-installer.js?v=', false)
            ->assertSee('id="installAppBtn"', false)
            ->assertSee('id="installAppModal"', false)
            ->assertSee('Pasang Aplikasi', false);
    }

    /**
     * @return array<string, mixed>
     */
    private function manifest(): array
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);

        $this->assertIsArray($manifest, 'public/manifest.json harus berupa JSON yang valid.');

        return $manifest;
    }
}
