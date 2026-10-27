<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Lesson $lesson): RedirectResponse
    {
        $existing = $request->user()->favorites()->where('lesson_id', $lesson->id)->first();

        $existing
            ? $existing->delete()
            : $request->user()->favorites()->create(['lesson_id' => $lesson->id]);

        return back()->with('status', $existing ? 'Removed from your saved classes.' : 'Saved for later.');
    }
}
