<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Live subscriptions, evaluated in PHP (small dataset, exact logic).
        $live = Subscription::with('plan', 'user')
            ->whereIn('status', ['trialing', 'active', 'past_due'])
            ->get()
            ->filter(fn ($s) => $s->isCurrent() && $s->user && ! $s->user->isAdmin());

        $activeMembers = $live->pluck('user_id')->unique()->count();

        // MRR in cents: monthly at face value, yearly divided by 12.
        $mrrCents = (int) $live->sum(fn ($s) => match ($s->plan->interval->value) {
            'monthly' => $s->plan->price_cents,
            'yearly' => (int) round($s->plan->price_cents / 12),
            default => 0,
        });

        $trialsNow = $live->filter(fn ($s) => $s->isTrial())->count();

        // Signups per week — last 8 weeks of member registrations.
        $weeks = collect(range(7, 0))->map(function ($i) {
            $start = now()->startOfWeek()->subWeeks($i);

            return [
                'label' => $start->format('j M'),
                'value' => User::where('role', 'member')
                    ->whereBetween('created_at', [$start, $start->copy()->addWeek()])
                    ->count(),
            ];
        });

        $mix = [
            'monthly' => $live->filter(fn ($s) => $s->plan->interval->value === 'monthly')->count(),
            'yearly' => $live->filter(fn ($s) => $s->plan->interval->value === 'yearly')->count(),
        ];

        $completionsThisWeek = LessonProgress::whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(7))
            ->count();

        $topLessons = LessonProgress::whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(7))
            ->selectRaw('lesson_id, count(*) as plays')
            ->groupBy('lesson_id')
            ->orderByDesc('plays')
            ->take(3)
            ->get()
            ->map(fn ($row) => [
                'lesson' => Lesson::with('program')->find($row->lesson_id),
                'plays' => $row->plays,
            ])
            ->filter(fn ($row) => $row['lesson']);

        $recent = User::where('role', 'member')->latest()->take(6)->get();

        return view('admin.dashboard', [
            'activeMembers' => $activeMembers,
            'mrrRand' => number_format($mrrCents / 100, 0, '.', ' '),
            'trialsNow' => $trialsNow,
            'completionsThisWeek' => $completionsThisWeek,
            'weeks' => $weeks,
            'mix' => $mix,
            'topLessons' => $topLessons,
            'recent' => $recent,
            'liveByUser' => $live->keyBy('user_id'),
        ]);
    }
}
