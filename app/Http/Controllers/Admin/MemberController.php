<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PlanInterval;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $members = User::where('role', 'member')
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.members.index', ['members' => $members, 'q' => $q]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin() && $user->id !== auth()->id(), 404);

        $user->load(['subscriptions.plan']);

        return view('admin.members.show', [
            'member' => $user,
            'plans' => Plan::orderBy('sort')->get(),
            'current' => $user->currentSubscription(),
            'completed' => $user->progress()->whereNotNull('completed_at')->count(),
            'lastActive' => $user->progress()->latest('updated_at')->first()?->updated_at,
        ]);
    }

    /** Record a received payment: activate/extend by one plan period. */
    public function grant(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'plan_key' => ['required', 'exists:plans,key'],
            'note' => ['nullable', 'string', 'max:150'],
        ]);

        $plan = Plan::where('key', $data['plan_key'])->firstOrFail();

        $subscription = $user->subscriptions()
            ->where('plan_id', $plan->id)
            ->whereIn('status', ['trialing', 'active', 'past_due'])
            ->latest('id')
            ->first();

        if (! $subscription) {
            $subscription = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active->value,
            ]);
        }

        if ($data['note'] ?? null) {
            $subscription->forceFill(['notes' => $data['note']])->save();
        }

        $subscription->extendOnePeriod();

        return back()->with('status', $plan->interval === PlanInterval::Once
            ? "{$user->firstName()} now owns the {$plan->name}."
            : "{$user->firstName()}'s membership is active until "
              .($subscription->fresh()->current_period_ends_at?->format('j M Y') ?? '—').'.');
    }

    public function end(Request $request, User $user): RedirectResponse
    {
        $subscription = $user->currentSubscription();

        if (! $subscription) {
            return back()->withErrors(['member' => 'No live membership to end.']);
        }

        $subscription->forceFill([
            'status' => SubscriptionStatus::Cancelled->value,
            'cancelled_at' => now(),
        ])->save();

        return back()->with('status', "{$user->firstName()}'s membership has been ended.");
    }

    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['member' => 'You can\'t change your own role.']);
        }

        $user->update(['role' => $user->isAdmin() ? 'member' : 'admin']);

        return back()->with('status', $user->isAdmin()
            ? "{$user->name} can now manage the studio."
            : "{$user->name} is a member again.");
    }
}
