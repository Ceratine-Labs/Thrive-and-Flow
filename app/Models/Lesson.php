<?php

namespace App\Models;

use App\Enums\Stage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_free_preview' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(ProgramWeek::class, 'program_week_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** "24:10" */
    public function durationLabel(): ?string
    {
        if (! $this->duration_seconds) {
            return null;
        }

        return sprintf('%d:%02d', intdiv($this->duration_seconds, 60), $this->duration_seconds % 60);
    }

    /**
     * Why a member can (or cannot) watch this class.
     * Returns 'ok' | 'login' | 'subscribe' | 'drip' | 'unpublished'.
     */
    public function accessFor(?User $user): string
    {
        if (! $this->is_published || ! $this->program->is_published) {
            return $user?->isAdmin() ? 'ok' : 'unpublished';
        }

        if ($this->is_free_preview) {
            return 'ok';
        }

        if (! $user) {
            return 'login';
        }

        if ($user->isAdmin()) {
            return 'ok';
        }

        if (! $user->hasStudioAccess()) {
            return 'subscribe';
        }

        // Week-by-week drip only applies to pregnancy programs for members
        // whose pregnancy week is known; everyone else roams freely.
        if ($this->program->drip_by_week
            && $this->program->stage === Stage::Pregnancy
            && $this->program_week_id
            && ($pw = $user->pregnancyWeek()) !== null
            && $this->week->week_number > $pw + 1) {
            return 'drip';
        }

        return 'ok';
    }

    public function watchableBy(?User $user): bool
    {
        return $this->accessFor($user) === 'ok';
    }

    /** The next published class in the same program, if any. */
    public function next(): ?self
    {
        return $this->program->lessons()
            ->where('is_published', true)
            ->where('position', '>', $this->position)
            ->orderBy('position')
            ->first();
    }
}
