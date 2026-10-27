<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /** Alternating class-pair titles for strength weeks. */
    private array $pairs = [
        ['Full-body strength', 'Strength & stability'],
        ['Lower body & glutes', 'Upper body & posture'],
        ['Squat & hinge patterns', 'Carries & core'],
        ['Strength endurance', 'Balance & mobility'],
    ];

    private array $pairDurations = [[1360, 1445], [1390, 1512], [1470, 1315], [1540, 1400]];

    private string $strengthMods = "All trimesters — take your squat stance wider than hip-width to give your bump room.\n"
        ."Late pregnancy — swap floor presses for incline push-ups on the bench.\n"
        ."Feeling dizzy? Use the side-lying option shown on screen.\n"
        .'Diastasis aware — exhale on effort, brace gently, never bear down.';

    public function run(): void
    {
        $this->trimesterProgram(
            slug: 'bump-strength-t1',
            title: 'Bump Strength · Trimester 1',
            subtitle: 'Gentle foundations while everything changes — energy-aware and nausea-friendly.',
            from: 6, to: 13, sort: 10, color: 'slate',
            phases: [[6, 13, 'Foundations · weeks 6–13']],
            themes: [
                'Gentle beginnings', 'Breath before load', 'Steady foundations', 'Energy-aware strength',
                'Posture & alignment', 'Quiet core work', 'Building the habit', 'Closing the first act',
            ],
        );

        $this->trimesterProgram(
            slug: 'bump-strength-t2',
            title: 'Bump Strength · Trimester 2',
            subtitle: 'Weeks 14–27 are your sweet spot — build steady strength while your bump grows.',
            from: 14, to: 27, sort: 20, color: 'slate',
            phases: [[14, 17, 'Settle in · weeks 14–17'], [18, 22, 'Build · weeks 18–22'], [23, 27, 'Sustain · weeks 23–27']],
            themes: [
                'Reconnect & align', 'Foundations under load', 'Steady strength', 'Posture & breath',
                'Lower body focus', 'Upper body & carries', 'Squat & hinge week', 'Core under control',
                'Strength endurance', 'Balance & stability', 'Open hips, strong back', 'Power without pressure',
                'Long & strong', 'Ready for the third act',
            ],
            freePreviewFirst: true,
        );

        $this->trimesterProgram(
            slug: 'bump-strength-t3',
            title: 'Bump Strength · Trimester 3',
            subtitle: 'Stamina, positioning and birth prep — strong to the finish line.',
            from: 28, to: 41, sort: 30, color: 'slate',
            phases: [[28, 31, 'Ground · weeks 28–31'], [32, 36, 'Sustain · weeks 32–36'], [37, 41, 'Soften & prepare · weeks 37–41']],
            themes: [
                'A new centre of gravity', 'Strong and steady', 'Stamina for the stretch', 'Hips that open',
                'Back-friendly strength', 'Breath under effort', 'Positions for birth', 'Calm, strong core',
                'Endurance week', 'Soften the front, strengthen the back', 'Practising patience', 'Gentle power',
                'Almost there', 'Ready when baby is',
            ],
        );

        $this->postnatalProgram();
        $this->collection(
            slug: 'mom-baby-flow', title: 'Mom & Baby Flow', stage: 'mom_baby', sort: 50, color: 'sand', icon: 'baby',
            subtitle: 'Your baby is part of the class — playful, giggly, surprisingly effective.',
            lessons: [
                ['Hello baby, hello body', 1187], ['Carry & squat', 1420], ['Tummy-time together', 1265],
                ['Sling & swing strength', 1490], ['Floor play & planks', 1330], ['Pram-free cardio', 1544],
                ['Giggles & glutes', 1276], ['Strong mama, calm baby', 1791],
            ],
            level: 'From 6 weeks postpartum', equipment: ['Mat', 'A blanket for baby'], freeFirst: true,
        );
        $this->collection(
            slug: 'mobility-studio', title: 'Mobility Studio', stage: 'any', sort: 60, color: 'slate', icon: 'wave',
            subtitle: 'Hips, back and shoulders — undo the aches of carrying, bump or baby.',
            lessons: [
                ['Morning unwind — full body', 887], ['Hips like hinges', 1013], ['Upper back release', 941],
                ['Pelvis & lower back', 1102], ['Shoulders & neck', 899], ['Side-body space', 1043],
                ['Ankles & feet (yes, really)', 947], ['Deep hip openers', 1121], ['Spine mobility flow', 1009], ['Evening wind-down', 1179],
            ],
            level: 'All stages', equipment: ['Mat', 'Yoga block (or books)'],
        );
        $this->collection(
            slug: 'breath-core-foundations', title: 'Breath & Core Foundations', stage: 'any', sort: 40, color: 'blush', icon: 'petal',
            subtitle: 'Diaphragmatic breathing in four positions — the base of every class in the studio.',
            lessons: [
                ['Diaphragmatic breathing — standing', 254], ['Diaphragmatic breathing — quadruped', 232],
                ['Diaphragmatic breathing — supine', 287], ['Diaphragmatic breathing — seated', 241],
            ],
            level: 'Start here', equipment: ['Nothing at all'], freeFirst: true,
        );
        $this->stretchLibrary();
        $this->warmupLibrary();
    }

    private function trimesterProgram(
        string $slug, string $title, string $subtitle, int $from, int $to, int $sort,
        string $color, array $phases, array $themes, bool $freePreviewFirst = false,
    ): void {
        $program = Program::updateOrCreate(['slug' => $slug], [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $subtitle.' Two focused classes a week, with your bump supported in every position and modifications shown throughout.',
            'stage' => 'pregnancy',
            'kind' => 'program',
            'weeks_from' => $from,
            'weeks_to' => $to,
            'classes_per_week' => 2,
            'level' => 'All levels',
            'drip_by_week' => true,
            'is_published' => true,
            'sort' => $sort,
            'color' => $color,
            'icon' => 'petal',
            'equipment' => ['Mat', 'Resistance band', 'Bench or chair', 'Light dumbbells', 'Pregnancy ball', 'Pilates ball (or pillow)'],
        ]);

        $position = 0;

        foreach (range($from, $to) as $i => $weekNumber) {
            $phase = collect($phases)->first(fn ($p) => $weekNumber >= $p[0] && $weekNumber <= $p[1]);

            $week = $program->weeks()->updateOrCreate(['week_number' => $weekNumber], [
                'theme' => $themes[$i] ?? 'Steady strength',
                'phase' => $phase[2] ?? null,
                'sort' => $i,
            ]);

            $pair = $this->pairs[$i % 4];
            $durations = $this->pairDurations[$i % 4];

            foreach ([0, 1] as $c) {
                Lesson::updateOrCreate(['slug' => "{$slug}-w{$weekNumber}-c".($c + 1)], [
                    'program_id' => $program->id,
                    'program_week_id' => $week->id,
                    'title' => 'Class '.($c + 1).' — '.$pair[$c],
                    'description' => "Week {$weekNumber} · {$week->theme}. ".
                        'Your coach cues breath and pelvic floor through every rep, so the strength you build supports your bump now and your recovery later.',
                    'modifications' => $this->strengthMods,
                    'duration_seconds' => $durations[$c] + ($i % 3) * 37,
                    'position' => $position++,
                    'is_free_preview' => $freePreviewFirst && $i === 0 && $c === 0,
                ]);
            }
        }
    }

    private function postnatalProgram(): void
    {
        $program = Program::updateOrCreate(['slug' => 'postnatal-rebuild'], [
            'title' => 'Postnatal Rebuild',
            'subtitle' => 'Core and pelvic floor first, then the rest — rebuild without rushing.',
            'description' => 'Sixteen classes that meet you where birth left you. Reconnect with your breath and deep core, then rebuild strength layer by layer — from first walks to full workouts.',
            'stage' => 'postpartum',
            'kind' => 'program',
            'classes_per_week' => 2,
            'level' => 'From 6 weeks postpartum',
            'drip_by_week' => false,
            'is_published' => true,
            'sort' => 35,
            'color' => 'blush',
            'icon' => 'wave',
            'equipment' => ['Mat', 'Resistance band', 'Pilates ball (or pillow)', 'Light dumbbells'],
        ]);

        $themes = [
            'Reconnect — breath & core', 'Gentle returns', 'Pelvic floor foundations', 'Standing strong again',
            'Carrying without the ache', 'Deep core, quiet strength', 'Stronger every week', 'Full workouts, safely',
        ];

        $position = 0;

        foreach ($themes as $i => $theme) {
            $week = $program->weeks()->updateOrCreate(['week_number' => $i + 1], [
                'theme' => $theme,
                'phase' => $i < 4 ? 'Reconnect · weeks 1–4' : 'Rebuild · weeks 5–8',
                'sort' => $i,
            ]);

            foreach ([0, 1] as $c) {
                $duration = [1123, 1345, 1489, 1560, 1672, 1810, 1935, 2141][$i] + $c * 96;
                Lesson::updateOrCreate(['slug' => 'postnatal-rebuild-w'.($i + 1).'-c'.($c + 1)], [
                    'program_id' => $program->id,
                    'program_week_id' => $week->id,
                    'title' => 'Class '.($c + 1).' — '.$theme,
                    'description' => 'Rebuild week '.($i + 1).' · '.$theme.'. Gentle, honest programming for a healing body — every move has an easier option.',
                    'modifications' => "C-section recovery — keep pressure low; take the inclined options until cleared.\n"
                        ."Leaking or heaviness? That's the pelvic floor asking for the gentler track — it's on screen.\n"
                        .'Interrupted by the baby? Pause. This class waits.',
                    'duration_seconds' => $duration,
                    'position' => $position++,
                    'is_free_preview' => $i === 0 && $c === 0,
                ]);
            }
        }
    }

    private function collection(
        string $slug, string $title, string $stage, int $sort, string $color, string $icon,
        string $subtitle, array $lessons, string $level, array $equipment, bool $freeFirst = false,
    ): void {
        $program = Program::updateOrCreate(['slug' => $slug], [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $subtitle,
            'stage' => $stage,
            'kind' => 'collection',
            'level' => $level,
            'is_published' => true,
            'sort' => $sort,
            'color' => $color,
            'icon' => $icon,
            'equipment' => $equipment,
        ]);

        foreach ($lessons as $i => [$name, $duration]) {
            Lesson::updateOrCreate(['slug' => $slug.'-'.Str::padLeft($i + 1, 2, '0')], [
                'program_id' => $program->id,
                'program_week_id' => null,
                'title' => $name,
                'description' => $title.' · '.$name.'.',
                'duration_seconds' => $duration,
                'position' => $i,
                'is_free_preview' => $freeFirst && $i === 0,
            ]);
        }
    }

    private function stretchLibrary(): void
    {
        $areas = [
            'Neck', 'Shoulders', 'Chest', 'Upper back', 'Lower back', 'Side body', 'Hips', 'Hip flexors',
            'Glutes', 'Hamstrings', 'Inner thighs', 'Calves', 'Ankles', 'Wrists', 'Full body',
        ];

        $lessons = [];
        foreach ($areas as $i => $area) {
            $lessons[] = [$area.' — seated stretch', 156 + ($i * 13) % 120];
            $lessons[] = [$area.' — supported stretch', 189 + ($i * 17) % 140];
        }

        $this->collection(
            slug: 'stretch-library', title: 'Stretch Library', stage: 'any', sort: 70, color: 'sand', icon: 'wave',
            subtitle: '30 targeted stretches to bolt onto any class — or any nap window.',
            lessons: $lessons, level: 'All stages', equipment: ['Mat'],
        );
    }

    private function warmupLibrary(): void
    {
        $moves = [
            'March in place', 'Shoulder rolls', 'Arm circles', 'Side reaches', 'Hip circles', 'Knee lifts',
            'Ankle rolls', 'Cat-cow', 'Bird-dog reach', 'Standing side bend', 'Wall push-away', 'Heel raises',
            'Step-touch', 'Gentle squat pulse', 'Torso rotations', 'Wrist circles', 'Neck half-moons',
            'Pelvic tilts', 'Band pull-aparts', 'Glute bridges', 'Seated leg extensions', 'Breath reset',
        ];

        $this->collection(
            slug: 'warmup-library', title: 'Warm-Up Library', stage: 'any', sort: 80, color: 'slate', icon: 'petal',
            subtitle: 'Every warm-up move, isolated — perfect your form one minute at a time.',
            lessons: collect($moves)->map(fn ($m, $i) => [$m, 36 + ($i * 7) % 44])->all(),
            level: 'Technique', equipment: ['Nothing at all'],
        );
    }
}
