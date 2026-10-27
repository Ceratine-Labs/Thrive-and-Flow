<?php

namespace App\Models;

use App\Enums\PlanInterval;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'due_date',
        'baby_born_at',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    private bool $accessMemoSet = false;

    private ?Subscription $accessMemo = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'due_date' => 'date',
            'baby_born_at' => 'date',
        ];
    }

    /* ---------------------------------------------------------------- roles */

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /* ---------------------------------------------------------- relations */

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /* ------------------------------------------------------------- access */

    /** The subscription currently granting studio access, if any. */
    public function currentSubscription(): ?Subscription
    {
        if (! $this->accessMemoSet) {
            $this->accessMemo = $this->subscriptions()
                ->with('plan')
                ->whereIn('status', ['trialing', 'active', 'past_due'])
                ->latest('id')
                ->get()
                ->first(fn (Subscription $s) => $s->isCurrent() && $s->plan->interval !== PlanInterval::Once);
            $this->accessMemoSet = true;
        }

        return $this->accessMemo;
    }

    /** Full studio access: an admin, or a live trial/active membership. */
    public function hasStudioAccess(): bool
    {
        return $this->isAdmin() || $this->currentSubscription() !== null;
    }

    /** Owns the once-off Exercise Guide. */
    public function ownsGuide(): bool
    {
        return $this->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->get()
            ->contains(fn (Subscription $s) => $s->plan->interval === PlanInterval::Once);
    }

    /* -------------------------------------------------------------- stage */

    /** Current pregnancy week (1–42) derived from the due date, or null. */
    public function pregnancyWeek(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        $daysToDue = now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);

        if ($daysToDue < -21) {
            return null; // well past the due date — likely postpartum now
        }

        return (int) max(1, min(42, 40 - floor($daysToDue / 7)));
    }

    /** Weeks postpartum (1-based) derived from the baby's birth date, or null. */
    public function postpartumWeek(): ?int
    {
        if (! $this->baby_born_at || $this->baby_born_at->isFuture()) {
            return null;
        }

        return (int) floor($this->baby_born_at->startOfDay()->diffInDays(now()->startOfDay()) / 7) + 1;
    }

    /** e.g. "Trimester 2 · Week 26" / "Postpartum · Week 9" / null */
    public function stageLabel(): ?string
    {
        if ($week = $this->pregnancyWeek()) {
            $trimester = $week <= 13 ? 1 : ($week <= 27 ? 2 : 3);

            return "Trimester {$trimester} · Week {$week}";
        }

        if ($week = $this->postpartumWeek()) {
            return "Postpartum · Week {$week}";
        }

        return null;
    }

    public function firstName(): string
    {
        return Str::of($this->name)->before(' ')->toString() ?: $this->name;
    }

    public function initials(): string
    {
        return Str::of($this->name)->explode(' ')->take(2)
            ->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->implode('');
    }
}
