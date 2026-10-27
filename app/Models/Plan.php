<?php

namespace App\Models;

use App\Enums\PlanInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interval' => PlanInterval::class,
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** "R249" — rand display without cents when whole. */
    public function priceRand(): string
    {
        $rand = $this->price_cents / 100;

        return 'R'.($rand == (int) $rand ? number_format($rand, 0, '.', ' ') : number_format($rand, 2, '.', ' '));
    }

    public function priceLine(): string
    {
        return $this->priceRand().$this->interval->suffix();
    }
}
