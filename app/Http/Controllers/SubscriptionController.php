<?php

namespace App\Http\Controllers;

use App\Enums\PlanInterval;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Self-serve start. No payment gateway is wired in yet, so:
     *  - first-time members start their 7-day free trial immediately;
     *  - returning members are queued for the owner to activate after
     *    payment (EFT / card handled off-platform for now — PayFast later).
     */
    public function start(Request $request, Plan $plan): RedirectResponse
    {
        $user = $request->user();

        if ($plan->interval === PlanInterval::Once) {
            if ($user->ownsGuide()) {
                return back()->with('status', 'You already own the Exercise Guide — find it in your studio.');
            }

            $user->subscriptions()->firstOrCreate(
                ['plan_id' => $plan->id, 'status' => SubscriptionStatus::PastDue->value],
                ['notes' => 'Guide purchase — awaiting payment'],
            );

            return back()->with('status', 'Guide reserved! You\'ll receive payment details by email, and it unlocks the moment payment lands.');
        }

        if ($user->currentSubscription()) {
            return redirect()->route('studio.dashboard')
                ->with('status', 'Your membership is already active — welcome back.');
        }

        $hadTrial = $user->subscriptions()->whereNotNull('trial_ends_at')->exists();

        if (! $hadTrial && $plan->trial_days > 0) {
            $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Trialing->value,
                'trial_ends_at' => now()->addDays($plan->trial_days),
            ]);

            return redirect()->route('studio.dashboard')
                ->with('status', "Your free week has started, {$user->firstName()} — the whole studio is open.");
        }

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::PastDue->value,
            'notes' => 'Awaiting payment — activate on receipt',
        ]);

        return redirect()->route('studio.dashboard')
            ->with('status', 'Almost there — payment details are on their way to your inbox, and your membership opens as soon as payment lands.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $request->user()->currentSubscription();

        if (! $subscription) {
            return back()->with('status', 'There\'s no active membership to cancel.');
        }

        $subscription->forceFill([
            'status' => SubscriptionStatus::Cancelled->value,
            'cancelled_at' => now(),
        ])->save();

        return back()->with('status', 'Membership cancelled — your progress is saved if you ever come back.');
    }
}
