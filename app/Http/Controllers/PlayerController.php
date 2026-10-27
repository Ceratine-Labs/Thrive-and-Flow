<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function __invoke(Request $request, Lesson $lesson): View|RedirectResponse
    {
        $lesson->load('program', 'week');
        $user = $request->user();

        switch ($lesson->accessFor($user)) {
            case 'login':
                return redirect()->guest(route('login'))
                    ->with('status', 'Log in (or start your free week) to watch this class.');
            case 'subscribe':
                return redirect()->route('pricing')
                    ->with('status', 'This class is part of the studio membership — your first week is free.');
            case 'drip':
                return redirect()->route('programs.show', $lesson->program)
                    ->with('status', 'This class unlocks when you reach week '.$lesson->week->week_number.' — not long now.');
            case 'unpublished':
                abort(404);
        }

        // Sidebar: the same week's classes, or a window of the collection.
        if ($lesson->week) {
            $sidebar = $lesson->week->lessons()->where('is_published', true)->get()
                ->each(fn ($l) => $l->setRelation('program', $lesson->program)->setRelation('week', $lesson->week));
            $nextWeek = $lesson->program->weeks()
                ->where('week_number', '>', $lesson->week->week_number)->first();
            $nextWeekLessons = $nextWeek
                ? $nextWeek->lessons()->where('is_published', true)->get()
                    ->each(fn ($l) => $l->setRelation('program', $lesson->program)->setRelation('week', $nextWeek))
                : collect();
        } else {
            $sidebar = $lesson->program->lessons()->where('is_published', true)
                ->whereBetween('position', [max(0, $lesson->position - 2), $lesson->position + 4])
                ->get()->each(fn ($l) => $l->setRelation('program', $lesson->program));
            $nextWeek = null;
            $nextWeekLessons = collect();
        }

        $progress = $user
            ? $user->progress()->where('lesson_id', $lesson->id)->first()
            : null;

        $doneIds = $user
            ? $user->progress()->whereNotNull('completed_at')
                ->whereIn('lesson_id', $sidebar->pluck('id'))->pluck('lesson_id')->flip()
            : collect();

        return view('studio.player', [
            'lesson' => $lesson,
            'program' => $lesson->program,
            'sidebar' => $sidebar,
            'nextWeek' => $nextWeek,
            'nextWeekLessons' => $nextWeekLessons,
            'progress' => $progress,
            'doneIds' => $doneIds,
            'isFavorite' => $user ? $user->favorites()->where('lesson_id', $lesson->id)->exists() : false,
            'next' => $lesson->next(),
            'user' => $user,
        ]);
    }
}
