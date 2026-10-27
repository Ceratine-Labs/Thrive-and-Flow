<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramWeek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WeekController extends Controller
{
    public function store(Request $request, Program $program): RedirectResponse
    {
        $data = $request->validate([
            'week_number' => ['required', 'integer', 'between:1,42'],
            'theme' => ['required', 'string', 'max:120'],
            'phase' => ['nullable', 'string', 'max:120'],
        ]);

        if ($program->weeks()->where('week_number', $data['week_number'])->exists()) {
            return back()->withErrors(['week_number' => "Week {$data['week_number']} already exists in this program."]);
        }

        $program->weeks()->create($data + ['sort' => $data['week_number']]);

        return back()->with('status', "Week {$data['week_number']} added.");
    }

    public function update(Request $request, ProgramWeek $week): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'string', 'max:120'],
            'phase' => ['nullable', 'string', 'max:120'],
        ]);

        $week->update($data);

        return back()->with('status', $week->label().' updated.');
    }

    public function destroy(ProgramWeek $week): RedirectResponse
    {
        if ($week->lessons()->exists()) {
            return back()->withErrors(['week' => $week->label().' still has classes — move or delete them first.']);
        }

        $week->delete();

        return back()->with('status', 'Week removed.');
    }
}
