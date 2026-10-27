<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'key' => 'studio_monthly',
                'name' => 'Studio Membership',
                'description' => 'The full studio, billed monthly. Cancel anytime.',
                'price_cents' => 24900,
                'interval' => 'monthly',
                'trial_days' => 7,
                'sort' => 10,
            ],
            [
                'key' => 'studio_yearly',
                'name' => 'Studio Membership · Annual',
                'description' => 'A full year of the studio — two months free.',
                'price_cents' => 249000,
                'interval' => 'yearly',
                'trial_days' => 7,
                'sort' => 20,
            ],
            [
                'key' => 'guide_once',
                'name' => 'Pre & Postnatal Exercise Guide',
                'description' => '13 technique lessons you own forever.',
                'price_cents' => 34900,
                'interval' => 'once',
                'trial_days' => 0,
                'sort' => 30,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['key' => $plan['key']], $plan);
        }
    }
}
