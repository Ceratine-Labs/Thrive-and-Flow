<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort')->get();

        return view('pricing', [
            'monthly' => $plans->firstWhere('key', 'studio_monthly'),
            'yearly' => $plans->firstWhere('key', 'studio_yearly'),
            'guide' => $plans->firstWhere('key', 'guide_once'),
        ]);
    }
}
