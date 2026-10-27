<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function toggleComplete(Request $request, Lesson $lesson): RedirectResponse
    {
        $lesson->load('program', 'week');

        abort_unless($lesson->watchableBy($request->user()), 403);

        $row = $request->user()->progress()->firstOrCreate(['lesson_id' => $lesson->id]);

        $row->update([
            'completed_at' => $row->completed_at ? null : now(),
            'seconds_watched' => $row->completed_at ? $row->seconds_watched : ($lesson->duration_seconds ?? $row->seconds_watched),
        ]);

        return back()->with('status', $row->completed_at
            ? 'Nice work — class marked as done.'
            : 'Marked as not done.');
    }
}
