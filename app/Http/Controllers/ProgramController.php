<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $programs = Program::published()->ordered()->withCount([
            'lessons as published_lessons_count' => fn ($q) => $q->where('is_published', true),
        ])->get();

        $selectedStage = $request->query('stage', 'all');
        $selectedStage = $selectedStage === 'pregnant' ? 'pregnancy' : $selectedStage;
        if (! in_array($selectedStage, ['all', 'pregnancy', 'postpartum', 'mom_baby', 'any'], true)) {
            $selectedStage = 'all';
        }

        return view('programs.index', ['programs' => $programs, 'selectedStage' => $selectedStage]);
    }

    public function show(Request $request, Program $program): View
    {
        abort_unless($program->is_published || $request->user()?->isAdmin(), 404);

        $program->load([
            'weeks.lessons' => fn ($q) => $q->where('is_published', true),
            'lessons' => fn ($q) => $q->where('is_published', true),
        ]);

        // Pre-wire relations so access checks don't re-query per lesson.
        foreach ($program->weeks as $week) {
            foreach ($week->lessons as $lesson) {
                $lesson->setRelation('program', $program)->setRelation('week', $week);
            }
        }
        foreach ($program->lessons as $lesson) {
            $lesson->setRelation('program', $program);
        }

        $user = $request->user();

        $completed = $user
            ? $user->progress()->whereNotNull('completed_at')
                ->whereIn('lesson_id', $program->lessons->pluck('id'))->pluck('lesson_id')->flip()
            : collect();

        return view('programs.show', [
            'program' => $program,
            'completed' => $completed,
            'user' => $user,
            'guide' => array_replace(config('course_guides.defaults'), config('course_guides.courses.'.$program->slug, [])),
            'safety' => config('course_guides.safety.'.($program->stage->value === 'mom_baby' ? 'postpartum' : $program->stage->value)),
        ]);
    }
}
