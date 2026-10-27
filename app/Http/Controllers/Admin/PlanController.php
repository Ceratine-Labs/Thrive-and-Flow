<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::orderBy('sort')->withCount('subscriptions')->get(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price_rand' => ['required', 'numeric', 'min:0', 'max:100000'],
            'trial_days' => ['required', 'integer', 'between:0,60'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price_cents' => (int) round($data['price_rand'] * 100),
            'trial_days' => $data['trial_days'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', "{$plan->name} saved.");
    }
}
