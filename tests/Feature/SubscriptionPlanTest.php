<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user can select and update to 'standard' plan.
     */
    public function test_user_can_select_and_update_to_standard_plan(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'onboarding_completed' => true]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.subscription.update'), [
                'plan' => 'standard',
            ]);

        $response->assertStatus(302);
        
        $user->refresh();
        $this->assertEquals('standard', $user->plan);
    }

    /**
     * Test that free plan users are restricted to 1 event per year.
     */
    public function test_free_plan_users_are_restricted_to_one_event_per_year(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'onboarding_completed' => true]);

        // Create first event (should succeed)
        $this->actingAs($user)
            ->post(route('dashboard.events.store'), [
                'name' => 'Event 1',
                'description' => 'Description 1',
                'date' => now()->format('Y-m-d'),
            ])->assertSessionHas('success');

        $this->assertEquals(1, $user->events()->count());

        // Create second event (should fail and redirect with error)
        $response = $this->actingAs($user)
            ->post(route('dashboard.events.store'), [
                'name' => 'Event 2',
                'description' => 'Description 2',
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('dashboard.events.index'));
        $response->assertSessionHas('error');
        $this->assertEquals(1, $user->events()->count());
    }

    /**
     * Test that standard plan users are restricted to 5 events per year.
     */
    public function test_standard_plan_users_are_restricted_to_five_events_per_year(): void
    {
        $user = User::factory()->create(['plan' => 'standard', 'onboarding_completed' => true]);

        // Create 5 events
        for ($i = 1; $i <= 5; $i++) {
            $this->actingAs($user)
                ->post(route('dashboard.events.store'), [
                    'name' => "Event $i",
                    'description' => "Description $i",
                    'date' => now()->format('Y-m-d'),
                ])->assertSessionHas('success');
        }

        $this->assertEquals(5, $user->events()->count());

        // Create 6th event (should fail and redirect with error)
        $response = $this->actingAs($user)
            ->post(route('dashboard.events.store'), [
                'name' => 'Event 6',
                'description' => 'Description 6',
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('dashboard.events.index'));
        $response->assertSessionHas('error');
        $this->assertEquals(5, $user->events()->count());
    }

    /**
     * Test that user can skip plan selection during onboarding.
     */
    public function test_user_can_skip_plan_selection_during_onboarding(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'onboarding_completed' => false,
            'onboarding_step' => 2,
        ]);

        $response = $this->actingAs($user)
            ->get(route('onboarding.skip-plan'));

        $response->assertRedirect(route('dashboard.index'));
        
        $user->refresh();
        $this->assertEquals('none', $user->plan);
        $this->assertTrue((bool)$user->onboarding_completed);
    }

    /**
     * Test that user with plan 'none' can browse the dashboard but cannot perform mutating actions.
     */
    public function test_user_with_none_plan_can_browse_but_cannot_perform_mutating_actions(): void
    {
        $user = User::factory()->create([
            'plan' => 'none',
            'onboarding_completed' => true,
        ]);

        // GET request is allowed
        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertStatus(200);

        // POST request is intercepted and redirected
        $response = $this->actingAs($user)
            ->post(route('dashboard.events.store'), [
                'name' => 'Should Not Work',
                'description' => 'Will Fail',
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('dashboard.subscription.index'));
        $response->assertSessionHas('error');
        $this->assertEquals(0, $user->events()->count());
    }
}
