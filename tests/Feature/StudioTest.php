<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Plan;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Database\Seeders\ProgramSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PlanSeeder::class, ProgramSeeder::class]);
    }

    private function memberWithTrial(array $states = []): User
    {
        $user = User::factory()->create($states);
        $user->subscriptions()->create([
            'plan_id' => Plan::where('key', 'studio_monthly')->first()->id,
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(5),
        ]);

        return $user;
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/programs', '/programs/bump-strength-t2', '/pricing', '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_guest_can_watch_a_free_preview_but_not_a_members_class(): void
    {
        $preview = Lesson::where('slug', 'bump-strength-t2-w14-c1')->firstOrFail();
        $locked = Lesson::where('slug', 'bump-strength-t2-w14-c2')->firstOrFail();

        $this->get(route('studio.watch', $preview))->assertOk();
        $this->get(route('studio.watch', $locked))->assertRedirect(route('login'));
    }

    public function test_member_without_subscription_is_sent_to_pricing(): void
    {
        $user = User::factory()->create();
        $locked = Lesson::where('slug', 'bump-strength-t2-w14-c2')->firstOrFail();

        $this->actingAs($user)->get(route('studio.watch', $locked))
            ->assertRedirect(route('pricing'));
    }

    public function test_trial_member_can_watch_and_drip_blocks_future_weeks(): void
    {
        $user = $this->memberWithTrial(['due_date' => now()->addWeeks(14)->toDateString()]); // week 26

        $current = Lesson::where('slug', 'bump-strength-t2-w26-c1')->firstOrFail();
        $future = Lesson::where('slug', 'bump-strength-t3-w30-c1')->firstOrFail();

        $this->actingAs($user)->get(route('studio.watch', $current))->assertOk();
        $this->actingAs($user)->get(route('studio.watch', $future))
            ->assertRedirect(route('programs.show', $future->program));
    }

    public function test_postpartum_member_roams_pregnancy_programs_freely(): void
    {
        $user = $this->memberWithTrial(['baby_born_at' => now()->subWeeks(9)->toDateString()]);

        $lesson = Lesson::where('slug', 'bump-strength-t3-w30-c1')->firstOrFail();

        $this->actingAs($user)->get(route('studio.watch', $lesson))->assertOk();
    }

    public function test_member_can_mark_a_class_complete(): void
    {
        $user = $this->memberWithTrial();
        $lesson = Lesson::where('slug', 'postnatal-rebuild-w1-c1')->firstOrFail();

        $this->actingAs($user)->post(route('studio.complete', $lesson))->assertRedirect();

        $this->assertNotNull($user->progress()->where('lesson_id', $lesson->id)->first()?->completed_at);
    }

    public function test_registration_creates_a_member_with_their_stage(): void
    {
        $response = $this->post(route('register.attempt'), [
            'name' => 'Thandi Dlamini',
            'email' => 'thandi@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'stage' => 'pregnant',
            'due_date' => now()->addWeeks(20)->toDateString(),
        ]);

        $response->assertRedirect(route('pricing'));

        $user = User::where('email', 'thandi@example.com')->firstOrFail();
        $this->assertFalse($user->isAdmin());
        $this->assertSame(20, $user->pregnancyWeek());
        $this->assertNotNull($user->due_date);
    }

    public function test_subscribing_starts_a_trial_once(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('studio.subscribe', 'studio_monthly'))
            ->assertRedirect(route('studio.dashboard'));

        $this->assertTrue($user->fresh()->hasStudioAccess());
    }

    public function test_dashboard_requires_login_and_renders_for_members(): void
    {
        $this->get('/studio')->assertRedirect(route('login'));

        $user = $this->memberWithTrial(['due_date' => now()->addWeeks(14)->toDateString()]);
        $this->actingAs($user)->get('/studio')->assertOk();
    }

    public function test_admin_area_is_gated_by_role(): void
    {
        $member = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($member)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/programs')->assertOk();
        $this->actingAs($admin)->get('/admin/members')->assertOk();
        $this->actingAs($admin)->get('/admin/plans')->assertOk();
    }

    public function test_admin_can_create_a_program_and_grant_a_membership(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.programs.store'), [
            'title' => 'Birth Prep Breathwork',
            'stage' => 'pregnancy',
            'kind' => 'collection',
            'color' => 'blush',
            'icon' => 'petal',
        ])->assertRedirect();

        $this->assertNotNull(Program::where('slug', 'birth-prep-breathwork')->first());

        $this->actingAs($admin)->post(route('admin.members.grant', $member), [
            'plan_key' => 'studio_monthly',
            'note' => 'EFT ref 001',
        ])->assertRedirect();

        $this->assertTrue($member->fresh()->hasStudioAccess());
    }

    public function test_admin_builder_renders_for_a_seeded_program(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::where('slug', 'bump-strength-t2')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.programs.builder', $program))->assertOk();
    }
}
