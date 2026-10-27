<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StudioController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // Continue watching — the most recent unfinished class.
        $continue = $user->progress()
            ->whereNull('completed_at')
            ->where('seconds_watched', '>', 0)
            ->whereHas('lesson', fn ($q) => $q->where('is_published', true))
            ->with('lesson.program', 'lesson.week')
            ->latest('updated_at')
            ->first();

        // This week's plan.
        [$planLessons, $planTitle] = $this->weekPlan($user);
        $doneIds = $user->progress()->whereNotNull('completed_at')->pluck('lesson_id')->flip();

        // Programs the member has touched.
        $touchedProgramIds = $user->progress()->with('lesson')->get()
            ->pluck('lesson.program_id')->unique()->values();

        $myPrograms = Program::published()->ordered()
            ->whereIn('id', $touchedProgramIds)
            ->get()
            ->map(fn ($p) => ['program' => $p, 'percent' => $p->progressPercentFor($user)]);

        // Simple streak: consecutive days (ending today or yesterday) with a completion.
        $days = $user->progress()->whereNotNull('completed_at')->get()
            ->map(fn ($p) => $p->completed_at->toDateString())->unique()->sort()->values();
        $streak = 0;
        $cursor = now()->startOfDay();
        if (! $days->contains($cursor->toDateString())) {
            $cursor = $cursor->subDay();
        }
        while ($days->contains($cursor->toDateString())) {
            $streak++;
            $cursor = $cursor->subDay();
        }

        return view('studio.dashboard', [
            'user' => $user,
            'continueRow' => $continue,
            'planLessons' => $planLessons,
            'planTitle' => $planTitle,
            'doneIds' => $doneIds,
            'myPrograms' => $myPrograms,
            'totalDone' => $doneIds->count(),
            'streak' => $streak,
            'favorites' => $user->favorites()->with('lesson.program')->latest()->take(4)->get(),
            'subscription' => $user->currentSubscription(),
        ]);
    }

    /** @return array{0: Collection, 1: string} */
    private function weekPlan($user): array
    {
        if ($week = $user->pregnancyWeek()) {
            $lessons = Lesson::published()
                ->whereHas('week', fn ($q) => $q->where('week_number', $week))
                ->whereHas('program', fn ($q) => $q->where('stage', Stage::Pregnancy->value)->where('is_published', true))
                ->with('program', 'week')
                ->orderBy('position')
                ->get();

            if ($lessons->isNotEmpty()) {
                return [$lessons, "Week {$week} · your plan"];
            }
        }

        // Postpartum (or unknown stage): the next unfinished Rebuild classes.
        $done = $user->progress()->whereNotNull('completed_at')->pluck('lesson_id');

        $lessons = Lesson::published()
            ->whereHas('program', fn ($q) => $q->where('slug', 'postnatal-rebuild'))
            ->whereNotIn('id', $done)
            ->with('program', 'week')
            ->orderBy('position')
            ->take(2)
            ->get();

        if ($lessons->isNotEmpty()) {
            return [$lessons, 'Next up in Postnatal Rebuild'];
        }

        return [Lesson::published()->with('program')->orderBy('id')->take(2)->get(), 'Suggested this week'];
    }
}
