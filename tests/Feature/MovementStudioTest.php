<?php

namespace Tests\Feature;

use Tests\TestCase;

class MovementStudioTest extends TestCase
{
    public function test_movement_guide_renders_with_accessible_controls_sources_and_text_fallback(): void
    {
        $view = $this->view('partials.movement-studio');

        $view->assertSee('id="movement"', false)
            ->assertSee('aria-label="Choose a movement"', false)
            ->assertSee('aria-label="Play movement demonstration"', false)
            ->assertSee('data-ms-dialog', false)
            ->assertSee('<noscript>', false)
            ->assertSee('The animated viewer needs JavaScript.')
            ->assertSee('not a pain map')
            ->assertSee('Stop and contact your maternity care team')
            ->assertSee('https://www.acog.org/womens-health/faqs/exercise-during-pregnancy', false);

        foreach (config('movements') as $key => $movement) {
            $view->assertSee('data-ms-choice="'.$key.'"', false)
                ->assertSee($movement['title'])
                ->assertSee($movement['source'], false);

            $this->assertCount(3, $movement['steps']);
            $this->assertFileExists(public_path($movement['image']));
        }
    }
}
