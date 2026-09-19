<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostHogAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'posthog.enabled' => true,
            'posthog.key' => 'ph_test_key',
            'posthog.host' => 'http://posthog.test',
            'posthog.session_replay_enabled' => true,
        ]);
    }

    public function test_analytics_is_absent_when_not_configured(): void
    {
        config(['posthog.enabled' => false]);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('today'))
            ->assertOk()
            ->assertDontSee('posthog.init', false);
    }

    public function test_analytics_identifies_only_the_internal_user_id_and_masks_replay_content(): void
    {
        $user = User::factory()->create([
            'name' => 'Private Agent Name',
            'email' => 'private-agent@example.test',
        ]);

        $this->actingAs($user)->get(route('today'))
            ->assertOk()
            ->assertSee('posthog.init', false)
            ->assertSee('posthog.identify('.$user->id.')', false)
            ->assertSee('autocapture: false', false)
            ->assertSee('capture_pageview: false', false)
            ->assertSee('maskAllInputs: true', false)
            ->assertSee("maskTextSelector: '*'", false)
            ->assertSee("blockSelector: 'img'", false)
            ->assertDontSee($user->name)
            ->assertDontSee($user->email);
    }

    public function test_successful_registration_and_login_queue_only_their_expected_events(): void
    {
        $this->post(route('register.store'), [
            'email' => 'signup@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('today'))
            ->assertSessionHas('posthog_events', ['signup_completed']);

        $this->post(route('logout'));
        $user = User::factory()->create(['password' => 'secure-password']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertRedirect(route('today'))
            ->assertSessionHas('posthog_events', ['login_completed']);
    }

    public function test_successful_workflow_events_are_queued_without_record_payloads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('properties.store'), [
            'transaction_type' => 'sale',
            'name' => 'Sensitive Property Name',
            'price' => 3500000,
            'bedrooms' => 2,
            'location' => 'Sensitive Location',
        ])->assertRedirect()
            ->assertSessionHas('posthog_events', ['first_property_created']);

        $this->actingAs($user)->post(route('clients.store'), [
            'name' => 'Sensitive Client Name',
            'transaction_type' => 'buy',
            'budget' => 3500000,
            'locations' => 'Sensitive Location',
        ])->assertRedirect()
            ->assertSessionHas('posthog_events', ['first_client_created']);

        $client = Client::query()->firstOrFail();
        $property = Property::query()->firstOrFail();
        $this->actingAs($user)->post(route('appointments.store'), [
            'client_id' => $client->id,
            'property_id' => $property->id,
            'appointment_date' => today()->toDateString(),
            'appointment_time' => '10:00',
        ])->assertRedirect()
            ->assertSessionHas('posthog_events', ['appointment_created']);

        $this->actingAs($user)->post(route('feedback.store'), [
            'type' => 'feature',
            'message' => 'Sensitive feedback message',
        ])->assertRedirect()
            ->assertSessionHas('posthog_events', ['feedback_submitted']);
    }

    public function test_match_upgrade_and_free_limit_events_are_queued_only_when_reached(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create([
            'transaction_type' => 'buy',
            'budget' => 3500000,
            'locations' => 'สุขุมวิท',
        ]);
        $property = Property::factory()->for($user)->create([
            'transaction_type' => 'sale',
            'price' => 3500000,
            'location' => 'สุขุมวิท',
        ]);

        $this->actingAs($user)->get(route('clients.show', $client))
            ->assertOk()
            ->assertSessionHas('posthog_events', ['match_viewed']);

        $this->actingAs($user)->get(route('upgrade'))
            ->assertOk()
            ->assertSessionHas('posthog_events', ['upgrade_viewed']);

        Property::factory()->count((int) config('plans.free.limits.properties') - 1)->for($user)->create();
        $this->actingAs($user)->post(route('properties.store'), [
            'transaction_type' => 'sale',
            'name' => 'Another property',
            'price' => 3500000,
            'bedrooms' => 2,
            'location' => 'สุขุมวิท',
        ])->assertRedirect(route('properties.create'))
            ->assertSessionHas('posthog_events', ['free_limit_reached']);
    }

    public function test_follow_up_json_response_returns_new_event_once_and_not_for_deduplicated_follow_up(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();
        $headers = ['Accept' => 'application/json'];

        $this->actingAs($user)->post(route('clients.followups.store', $client), ['days' => 1], $headers)
            ->assertOk()
            ->assertJsonPath('posthog_events', ['followup_created']);

        $this->actingAs($user)->post(route('clients.followups.store', $client), ['days' => 1], $headers)
            ->assertOk()
            ->assertJsonPath('posthog_events', []);
    }
}
