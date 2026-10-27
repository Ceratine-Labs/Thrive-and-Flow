<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::ordered()->withCount([
            'lessons',
            'lessons as published_lessons_count' => fn ($q) => $q->where('is_published', true),
        ])->get();

        return view('admin.programs.index', ['programs' => $programs]);
    }

    public function create(): View
    {
        return view('admin.programs.form', ['program' => new Program]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['slug'] = $this->uniqueSlug($data['title']);

        $program = Program::create($data);

        return redirect()->route('admin.programs.builder', $program)
            ->with('status', 'Program created — now add its weeks and classes.');
    }

    public function builder(Program $program): View
    {
        $program->load(['weeks.lessons', 'lessons.week']);

        return view('admin.programs.builder', ['program' => $program]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $program->update($this->validated($request));

        return back()->with('status', 'Program details saved.');
    }

    public function togglePublish(Program $program): RedirectResponse
    {
        $program->update(['is_published' => ! $program->is_published]);

        return back()->with('status', $program->is_published
            ? "\"{$program->title}\" is now live for members."
            : "\"{$program->title}\" is hidden from members.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stage' => ['required', Rule::in(['pregnancy', 'postpartum', 'mom_baby', 'any'])],
            'kind' => ['required', Rule::in(['program', 'collection'])],
            'weeks_from' => ['nullable', 'integer', 'between:1,42'],
            'weeks_to' => ['nullable', 'integer', 'between:1,42', 'gte:weeks_from'],
            'classes_per_week' => ['nullable', 'integer', 'between:1,7'],
            'level' => ['nullable', 'string', 'max:60'],
            'drip_by_week' => ['nullable', 'boolean'],
            'color' => ['required', Rule::in(['slate', 'blush', 'sand'])],
            'icon' => ['required', Rule::in(['petal', 'wave', 'baby'])],
            'equipment_text' => ['nullable', 'string', 'max:500'],
            'sort' => ['nullable', 'integer', 'between:0,999'],
        ]);

        $data['drip_by_week'] = $request->boolean('drip_by_week');
        $data['sort'] = $data['sort'] ?? 50;
        $data['equipment'] = collect(explode(',', $data['equipment_text'] ?? ''))
            ->map(fn ($e) => trim($e))->filter()->values()->all() ?: null;

        unset($data['equipment_text']);

        return $data;
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'program';
        $slug = $base;
        $n = 2;

        while (Program::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
