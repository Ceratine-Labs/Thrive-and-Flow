<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Plan;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $programs = Program::published()->ordered()->withCount([
            'lessons as published_lessons_count' => fn ($q) => $q->where('is_published', true),
        ])->get();

        $featured = collect(['bump-strength-t2', 'postnatal-rebuild', 'mom-baby-flow'])
            ->map(fn ($slug) => $programs->firstWhere('slug', $slug))
            ->filter()
            ->whenEmpty(fn () => $programs->take(3));

        return view('home', [
            'featured' => $featured,
            'lessonCount' => Lesson::published()->count(),
            'monthly' => Plan::where('key', 'studio_monthly')->first(),
        ]);
    }
}
