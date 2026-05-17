<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Panelist;
use App\Notifications\PanelistInvitation;
use App\Notifications\PanelistAddedToEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PanelistManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test adding a panelist for the first time creates the user with a temporary password and must_change_password = true.
     */
    public function test_adding_new_panelist_creates_account_and_sends_invitation(): void
    {
        Notification::fake();

        $moderator = User::factory()->create(['role' => 'moderator', 'onboarding_completed' => true]);
        $event = Event::factory()->create(['user_id' => $moderator->id]);

        $response = $this->actingAs($moderator)
            ->post(route('dashboard.events.panelists.store', $event->id), [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'sector' => 'Technology',
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'role' => 'panelist',
            'must_change_password' => true,
        ]);

        $user = User::where('email', 'john.doe@example.com')->firstOrFail();
        
        $this->assertDatabaseHas('panelists', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'sector' => 'Technology',
        ]);

        Notification::assertSentTo(
            $user,
            PanelistInvitation::class,
            function ($notification, $channels) use ($event) {
                return $notification->toMail($user)->viewData['eventName'] === $event->name;
            }
        );
    }

    /**
     * Test adding an existing panelist doesn't recreate/reset password, and sends AddedToEvent notification.
     */
    public function test_adding_existing_panelist_does_not_reset_password_and_sends_added_notification(): void
    {
        Notification::fake();

        $moderator = User::factory()->create(['role' => 'moderator', 'onboarding_completed' => true]);
        $event = Event::factory()->create(['user_id' => $moderator->id]);

        // Pre-create the panelist user with a known password
        $existingUser = User::factory()->create([
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'role' => 'panelist',
            'must_change_password' => false,
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($moderator)
            ->post(route('dashboard.events.panelists.store', $event->id), [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'sector' => 'Science',
            ]);

        $response->assertStatus(302);

        // Verify password is NOT changed/reset
        $existingUser->refresh();
        $this->assertTrue(Hash::check('secret123', $existingUser->password));
        $this->assertFalse($existingUser->must_change_password);

        $this->assertDatabaseHas('panelists', [
            'event_id' => $event->id,
            'user_id' => $existingUser->id,
            'sector' => 'Science',
        ]);

        Notification::assertSentTo(
            $existingUser,
            PanelistAddedToEvent::class,
            function ($notification, $channels) use ($event) {
                return $notification->toMail($existingUser)->viewData['eventName'] === $event->name;
            }
        );

        Notification::assertNotSentTo($existingUser, PanelistInvitation::class);
    }

    /**
     * Test middleware redirects users who must change their password.
     */
    public function test_middleware_redirects_users_who_must_change_password(): void
    {
        $panelist = User::factory()->create([
            'role' => 'panelist',
            'must_change_password' => true,
            'onboarding_completed' => true,
        ]);

        // Try to access panelist index
        $response = $this->actingAs($panelist)
            ->get(route('panelist.index'));

        $response->assertRedirect(route('auth.force-change-password'));
    }

    /**
     * Test forced password change allows access after successful change.
     */
    public function test_forced_password_change_works_correctly(): void
    {
        $panelist = User::factory()->create([
            'role' => 'panelist',
            'must_change_password' => true,
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($panelist)
            ->post(route('auth.force-change-password.post'), [
                'password' => 'newsecurepassword',
                'password_confirmation' => 'newsecurepassword',
            ]);

        $response->assertRedirect(route('panelist.index'));

        $panelist->refresh();
        $this->assertFalse($panelist->must_change_password);
        $this->assertTrue(Hash::check('newsecurepassword', $panelist->password));
    }
}
