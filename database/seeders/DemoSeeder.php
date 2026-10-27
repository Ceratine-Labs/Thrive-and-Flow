<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Plan;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Demo accounts + realistic activity so the studio and admin
     * screens have something true-to-life to show. Safe to re-run.
     */
    public function run(): void
    {
        $monthly = Plan::where('key', 'studio_monthly')->firstOrFail();
        $yearly = Plan::where('key', 'studio_yearly')->firstOrFail();

        // ---- The studio owner -------------------------------------------
        $admin = User::updateOrCreate(['email' => 'admin@thriveandflow.co.za'], [
            'name' => 'Thrive & Flow',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // ---- Naledi — trimester 2, week 26, active member ---------------
        $naledi = User::updateOrCreate(['email' => 'naledi@example.com'], [
            'name' => 'Naledi Mokoena',
            'password' => 'password',
            'role' => 'member',
            'due_date' => now()->addWeeks(14)->toDateString(), // week 26 of 40
        ]);
        $naledi->forceFill(['created_at' => now()->subDays(34)])->save();

        if (! $naledi->subscriptions()->exists()) {
            $naledi->subscriptions()->create([
                'plan_id' => $monthly->id,
                'status' => 'active',
                'current_period_ends_at' => now()->addDays(18),
                'notes' => 'Card via PayFast (demo)',
            ]);
        }

        $t2 = Program::where('slug', 'bump-strength-t2')->first();

        if ($t2) {
            $done = Lesson::where('program_id', $t2->id)
                ->whereHas('week', fn ($q) => $q->where('week_number', '<=', 25))
                ->orderBy('position')
                ->get();

            foreach ($done as $i => $lesson) {
                $naledi->progress()->updateOrCreate(['lesson_id' => $lesson->id], [
                    'seconds_watched' => $lesson->duration_seconds ?? 1400,
                    'completed_at' => now()->subDays(30)->addDays(intdiv($i, 1))->subHours(3),
                ]);
            }

            $current = Lesson::where('slug', 'bump-strength-t2-w26-c1')->first();
            if ($current) {
                $naledi->progress()->updateOrCreate(['lesson_id' => $current->id], [
                    'seconds_watched' => 852,
                    'completed_at' => null,
                ]);
            }
        }

        foreach (['breath-core-foundations-01', 'stretch-library-13'] as $slug) {
            if ($fav = Lesson::where('slug', $slug)->first()) {
                $naledi->favorites()->firstOrCreate(['lesson_id' => $fav->id]);
            }
        }

        // ---- Ayesha — postpartum week 9, on trial -----------------------
        $ayesha = User::updateOrCreate(['email' => 'ayesha@example.com'], [
            'name' => 'Ayesha Khan',
            'password' => 'password',
            'role' => 'member',
            'baby_born_at' => now()->subWeeks(9)->addDays(2)->toDateString(),
        ]);
        $ayesha->forceFill(['created_at' => now()->subDays(5)])->save();

        if (! $ayesha->subscriptions()->exists()) {
            $ayesha->subscriptions()->create([
                'plan_id' => $monthly->id,
                'status' => 'trialing',
                'trial_ends_at' => now()->addDays(2),
            ]);
        }

        foreach (['postnatal-rebuild-w1-c1', 'postnatal-rebuild-w1-c2'] as $i => $slug) {
            if ($lesson = Lesson::where('slug', $slug)->first()) {
                $ayesha->progress()->updateOrCreate(['lesson_id' => $lesson->id], [
                    'seconds_watched' => $lesson->duration_seconds,
                    'completed_at' => now()->subDays(3 - $i),
                ]);
            }
        }

        // ---- Bianca — trimester 1, fresh trial --------------------------
        $bianca = User::updateOrCreate(['email' => 'bianca@example.com'], [
            'name' => 'Bianca Retief',
            'password' => 'password',
            'role' => 'member',
            'due_date' => now()->addWeeks(31)->toDateString(), // week 9
        ]);
        $bianca->forceFill(['created_at' => now()->subDays(2)])->save();

        if (! $bianca->subscriptions()->exists()) {
            $bianca->subscriptions()->create([
                'plan_id' => $monthly->id,
                'status' => 'trialing',
                'trial_ends_at' => now()->addDays(5),
            ]);
        }

        // ---- Background members so the admin charts breathe -------------
        if (User::where('email', 'like', 'demo-member-%')->doesntExist()) {
            $spread = [52, 47, 41, 36, 33, 27, 24, 19, 15, 11, 8, 4]; // days ago

            foreach ($spread as $i => $daysAgo) {
                $member = User::factory()->create([
                    'email' => 'demo-member-'.($i + 1).'@example.com',
                ]);
                $member->forceFill(['created_at' => now()->subDays($daysAgo)])->save();

                if ($i % 4 !== 3) { // three in four hold a live membership
                    $member->subscriptions()->create([
                        'plan_id' => $i % 3 === 0 ? $yearly->id : $monthly->id,
                        'status' => 'active',
                        'current_period_ends_at' => now()->addDays(5 + ($i * 7) % 25),
                    ]);
                }
            }
        }
    }
}
