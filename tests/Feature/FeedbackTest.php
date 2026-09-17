<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_short_feedback_without_reentering_identity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('feedback.store'), ['type' => 'feature', 'message' => 'อยากให้เลือกนัดจากหน้าลูกค้าได้เร็วขึ้น'])
            ->assertRedirect(route('feedback.create'))->assertSessionHas('success');
        $this->assertDatabaseHas('feedback', ['user_id' => $user->id, 'type' => 'feature', 'status' => 'new']);
    }

    public function test_feedback_message_is_limited_and_guest_is_protected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('feedback.store'), ['type' => 'bug', 'message' => str_repeat('x', 301)])->assertSessionHasErrors('message');
        Auth::logout();
        $this->get(route('feedback.create'))->assertRedirect(route('login'));
    }
}
