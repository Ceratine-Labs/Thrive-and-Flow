<?php

namespace App\Models;

use App\Enums\ProgramKind;
use App\Enums\Stage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'stage' => Stage::class,
            'kind' => ProgramKind::class,
            'drip_by_week' => 'boolean',
            'is_published' => 'boolean',
            'equipment' => 'array',
        ];
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(ProgramWeek::class)->orderBy('sort')->orderBy('week_number');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** "Weeks 14–27" / "Self-paced" */
    public function weeksLabel(): string
    {
        return $this->weeks_from && $this->weeks_to
            ? "Weeks {$this->weeks_from}–{$this->weeks_to}"
            : 'Self-paced';
    }

    /** e.g. "28 classes · 2×/week" */
    public function metaLine(): string
    {
        $count = $this->lessons()->where('is_published', true)->count();
        $line = $count.' '.($count === 1 ? 'class' : 'classes');

        if ($this->classes_per_week) {
            $line .= " · {$this->classes_per_week}×/week";
        }

        return $line;
    }

    /** 0–100 completion for a user. */
    public function progressPercentFor(User $user): int
    {
        $ids = $this->lessons()->where('is_published', true)->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        $done = $user->progress()->whereIn('lesson_id', $ids)->whereNotNull('completed_at')->count();

        return (int) round($done / $ids->count() * 100);
    }
}
