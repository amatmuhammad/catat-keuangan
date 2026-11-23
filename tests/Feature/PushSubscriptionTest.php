<?php

namespace Tests\Feature;

use App\Listeners\LogFailedWebPush;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushMessage;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->postJson('/push-subscriptions', $this->payload())
            ->assertUnauthorized();

        $this->deleteJson('/push-subscriptions', ['endpoint' => 'https://push.example.com/device-1'])
            ->assertUnauthorized();
    }

    public function test_user_can_store_a_push_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/push-subscriptions', $this->payload())
            ->assertNoContent();

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://push.example.com/device-1',
            'public_key' => 'public-key-value',
            'auth_token' => 'auth-token-value',
            'content_encoding' => 'aes128gcm',
            'subscribable_id' => $user->id,
            'subscribable_type' => $user->getMorphClass(),
        ]);
    }

    public function test_storing_the_same_endpoint_updates_the_existing_subscription(): void
    {
        $user = User::factory()->create();
        $user->updatePushSubscription('https://push.example.com/device-1', 'old-key', 'old-auth', 'aesgcm');

        $this->actingAs($user)
            ->postJson('/push-subscriptions', $this->payload())
            ->assertNoContent();

        $this->assertDatabaseCount('push_subscriptions', 1);
        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://push.example.com/device-1',
            'public_key' => 'public-key-value',
            'auth_token' => 'auth-token-value',
        ]);
    }

    public function test_invalid_payload_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/push-subscriptions', ['endpoint' => '', 'keys' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['endpoint', 'keys.p256dh', 'keys.auth']);

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_user_can_remove_his_own_subscription_only(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $user->updatePushSubscription('https://push.example.com/device-1', 'key', 'auth', 'aes128gcm');
        $other->updatePushSubscription('https://push.example.com/device-2', 'key', 'auth', 'aes128gcm');

        $this->actingAs($user)
            ->deleteJson('/push-subscriptions', ['endpoint' => 'https://push.example.com/device-2'])
            ->assertNoContent();

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://push.example.com/device-2',
            'subscribable_id' => $other->id,
        ]);

        $this->actingAs($user)
            ->deleteJson('/push-subscriptions', ['endpoint' => 'https://push.example.com/device-1'])
            ->assertNoContent();

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://push.example.com/device-1',
        ]);
    }

    public function test_failed_web_push_delivery_is_logged_for_troubleshooting(): void
    {
        Event::fake();
        Event::assertListening(NotificationFailed::class, LogFailedWebPush::class);

        Log::spy();

        $report = new MessageSentReport(
            new Request('POST', 'https://push.example.com/device-expired'),
            new Response(410),
            false,
            '410 Gone',
        );

        $subscription = new PushSubscription;
        $subscription->id = 42;
        $subscription->endpoint = 'https://push.example.com/device-expired';
        $subscription->subscribable_id = 7;
        $subscription->subscribable_type = 'users';

        (new LogFailedWebPush)->handle(new NotificationFailed(
            $report,
            $subscription,
            (new WebPushMessage)->title('Arus Kas Masuk')->body('+Rp 500.000'),
        ));

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $message, array $context): bool => str_contains($message, 'Web push')
                && $context['endpoint'] === 'https://push.example.com/device-expired'
                && $context['reason'] === '410 Gone'
                && $context['subscription_expired'] === true
                && $context['subscription_id'] === 42,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $endpoint = 'https://push.example.com/device-1'): array
    {
        return [
            'endpoint' => $endpoint,
            'keys' => [
                'p256dh' => 'public-key-value',
                'auth' => 'auth-token-value',
            ],
            'contentEncoding' => 'aes128gcm',
        ];
    }
}
