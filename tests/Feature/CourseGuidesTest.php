<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\ProgramSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseGuidesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProgramSeeder::class);
    }

    public function test_all_existing_courses_render_their_guides_and_published_curriculum(): void
    {
        foreach (Program::published()->get() as $program) {
            $response = $this->get(route('programs.show', $program));
            $response->assertOk()
                ->assertSee('What to expect')
                ->assertSee('What you’ll practise')
                ->assertSee('A simple session rhythm')
                ->assertSee('Read the guidance')
                ->assertSee(config('course_guides.courses.'.$program->slug.'.focus'))
                ->assertSee(route('home', ['movement' => config('course_guides.courses.'.$program->slug.'.movement')]).'#movement', false);
        }
    }

    public function test_postnatal_readiness_is_explained_without_changing_stored_level(): void
    {
        $program = Program::where('slug', 'mom-baby-flow')->firstOrFail();

        $this->get(route('programs.index'))->assertOk()
            ->assertSee('Postnatal · readiness varies')
            ->assertDontSee('From 6 weeks postpartum');
        $this->get(route('programs.show', $program))->assertOk()
            ->assertSee('Postnatal · readiness varies')
            ->assertSee('not automatic clearance');
        $this->assertSame('From 6 weeks postpartum', $program->fresh()->level);
    }

    public function test_unpublished_programs_and_lessons_stay_hidden(): void
    {
        $program = Program::where('slug', 'bump-strength-t2')->firstOrFail();
        $lesson = $program->lessons()->firstOrFail();
        $lesson->update(['title' => 'PRIVATE LESSON SENTINEL', 'is_published' => false]);

        $this->get(route('programs.show', $program))->assertOk()->assertDontSee('PRIVATE LESSON SENTINEL');
        $program->update(['title' => 'PRIVATE PROGRAM SENTINEL', 'is_published' => false]);
        $this->get(route('programs.index'))->assertDontSee('PRIVATE PROGRAM SENTINEL');
        $this->get(route('programs.show', $program))->assertNotFound();
        $this->get(route('studio.watch', $lesson))->assertNotFound();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('programs.show', $program))->assertOk()->assertSee('What to expect');
    }

    public function test_new_course_gets_fallback_guidance_and_no_false_free_preview(): void
    {
        $program = Program::create([
            'slug' => 'new-library',
            'title' => 'New Library',
            'kind' => 'collection',
            'stage' => 'any',
            'is_published' => true,
            'color' => 'blush',
            'icon' => 'wave',
        ]);
        Lesson::create([
            'program_id' => $program->id,
            'slug' => 'new-members-class',
            'title' => 'Members class',
            'is_published' => true,
            'is_free_preview' => false,
        ]);

        $this->get(route('programs.show', $program))->assertOk()
            ->assertSee('Move at your pace')
            ->assertSee('View the first class')
            ->assertDontSee('Watch the free preview')
            ->assertSee('Members class');
        $this->get(route('programs.index'))->assertOk()->assertSee('New Library');
    }

    public function test_stage_query_initialises_catalog_without_removing_other_choices(): void
    {
        $this->get(route('programs.index', ['stage' => 'pregnant']))->assertOk()
            ->assertSee('data-initial-stage="pregnancy"', false)
            ->assertSee('Bump Strength')
            ->assertSee('Postnatal Rebuild');
        $this->get(route('programs.index', ['stage' => 'any']))->assertOk()
            ->assertSee('data-initial-stage="any"', false);
        $this->get(route('programs.index', ['stage' => 'unknown']))->assertOk()
            ->assertSee('data-initial-stage="all"', false);
    }
}
