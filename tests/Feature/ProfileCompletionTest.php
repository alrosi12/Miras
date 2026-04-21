<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_when_profile_is_incomplete(): void
    {
        $user = User::factory()->create([
            'weight' => null,
            'height' => null,
            'birth_date' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('profile.complete.show', absolute: false));
    }

    public function test_complete_profile_updates_user_and_allows_dashboard(): void
    {
        $user = User::factory()->create([
            'weight' => null,
            'height' => null,
            'birth_date' => null,
        ]);

        $response = $this->actingAs($user)->patch('/complete-profile', [
            'weight' => '80',
            'height' => '180',
            'birth_date' => '1990-01-10',
            'goal' => 'maintain',
        ]);

        $response->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertSame('80.00', $user->weight);
        $this->assertSame('180.00', $user->height);
        $this->assertNotNull($user->birth_date);
        $this->assertTrue($user->hasCompletedProfile());

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
