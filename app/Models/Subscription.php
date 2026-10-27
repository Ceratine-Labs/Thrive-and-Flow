<?php

namespace App\Models;

use App\Enums\PlanInterval;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'trial_ends_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /** Does this subscription grant access right now? */
    public function isCurrent(): bool
    {
        return match ($this->status) {
            SubscriptionStatus::Trialing => $this->trial_ends_at?->isFuture() ?? false,
            SubscriptionStatus::Active => $this->current_period_ends_at === null
                || $this->current_period_ends_at->isFuture(),
            // A missed payment keeps access for a short grace window.
            SubscriptionStatus::PastDue => $this->current_period_ends_at !== null
                && now()->lessThan($this->current_period_ends_at->copy()->addDays(7)),
            default => false,
        };
    }

    public function isTrial(): bool
    {
        return $this->status === SubscriptionStatus::Trialing;
    }

    /** Extend the paid period by one interval from now (or from the current end if later). */
    public function extendOnePeriod(): void
    {
        $days = $this->plan->interval->periodDays();

        if ($days === null) {
            $this->forceFill(['status' => SubscriptionStatus::Active, 'current_period_ends_at' => null])->save();

            return;
        }

        $base = $this->current_period_ends_at?->isFuture() ? $this->current_period_ends_at : now();

        $this->forceFill([
            'status' => SubscriptionStatus::Active,
            'current_period_ends_at' => $base->copy()->addDays($days),
        ])->save();
    }
}
