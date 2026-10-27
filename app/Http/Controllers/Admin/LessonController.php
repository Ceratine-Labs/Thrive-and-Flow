<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LessonController extends Controller
{
    public function store(Request $request, Program $program): RedirectResponse
    {
        $data = $this->validated($request, $program);

        $data['program_id'] = $program->id;
        $data['slug'] = $this->uniqueSlug($program->slug.'-'.$data['title']);
        $data['position'] = ((int) $program->lessons()->max('position')) + 1;

        Lesson::create($data);

        return back()->with('status', 'Class added.');
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $lesson->update($this->validated($request, $lesson->program));

        return back()->with('status', 'Class saved.');
    }

    public function togglePublish(Lesson $lesson): RedirectResponse
    {
        $lesson->update(['is_published' => ! $lesson->is_published]);

        return back()->with('status', $lesson->is_published ? 'Class is live.' : 'Class hidden.');
    }

    public function togglePreview(Lesson $lesson): RedirectResponse
    {
        $lesson->update(['is_free_preview' => ! $lesson->is_free_preview]);

        return back()->with('status', $lesson->is_free_preview
            ? 'Class is now a free preview — anyone can watch it.'
            : 'Class is members-only again.');
    }

    /** Swap positions with the neighbour above/below within the program. */
    public function move(Request $request, Lesson $lesson): RedirectResponse
    {
        $dir = $request->validate(['dir' => ['required', Rule::in(['up', 'down'])]])['dir'];

        $query = $lesson->program->lessons()
            ->when($lesson->program_week_id, fn ($q) => $q->where('program_week_id', $lesson->program_week_id));

        $neighbour = $dir === 'up'
            ? (clone $query)->where('position', '<', $lesson->position)->orderByDesc('position')->first()
            : (clone $query)->where('position', '>', $lesson->position)->orderBy('position')->first();

        if ($neighbour) {
            [$a, $b] = [$lesson->position, $neighbour->position];
            $lesson->update(['position' => $b]);
            $neighbour->update(['position' => $a]);
        }

        return back();
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return back()->with('status', 'Class deleted.');
    }

    private function validated(Request $request, Program $program): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'program_week_id' => ['nullable', Rule::exists('program_weeks', 'id')->where('program_id', $program->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'modifications' => ['nullable', 'string', 'max:2000'],
            'duration' => ['nullable', 'regex:/^\d{1,3}:\d{2}$/'],
            'video_provider' => ['nullable', Rule::in(['bunny', 'youtube', 'vimeo', 'file'])],
            'video_ref' => ['nullable', 'string', 'max:255'],
        ]);

        if (array_key_exists('duration', $data)) {
            if ($data['duration']) {
                [$m, $s] = explode(':', $data['duration']);
                $data['duration_seconds'] = ((int) $m) * 60 + (int) $s;
            }
            unset($data['duration']);
        }

        return $data;
    }

    private function uniqueSlug(string $source): string
    {
        $base = Str::slug(Str::limit($source, 60, '')) ?: 'class';
        $slug = $base;
        $n = 2;

        while (Lesson::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
