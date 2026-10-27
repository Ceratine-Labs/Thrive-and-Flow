<?php

// Companion guidance; the database remains the source of truth for lessons,
// equipment, week ranges, publishing and member access.
$strengthSession = [
    ['title' => 'Arrive & warm up', 'detail' => 'Check your energy, then begin with easy marching, shoulder rolls or the class warm-up.'],
    ['title' => 'Practise strength', 'detail' => 'Explore the lesson’s squat, hinge, posture or stability theme. Use comfortable effort and leave space for rest.'],
    ['title' => 'Ease down', 'detail' => 'Slow the pace and finish with an easy breath or a comfortable stretch. Notice how you feel afterwards.'],
];

$rebuildSession = [
    ['title' => 'Reconnect', 'detail' => 'Take an easy breath and choose a comfortable starting position. Begin with the first part of the lesson.'],
    ['title' => 'Build gradually', 'detail' => 'Practise the week’s core or standing-strength theme. Repeat a familiar section before adding more challenge.'],
    ['title' => 'Pause & recover', 'detail' => 'Allow time to rest afterwards. Choose your next session based on your recovery, rather than the calendar alone.'],
];

$librarySession = [
    ['title' => 'Choose a focus', 'detail' => 'Pick the body area or movement you want to explore. Read the class title and prepare any support.'],
    ['title' => 'Explore one class', 'detail' => 'Start with a small, comfortable movement. Pause the clip to practise at your own speed.'],
    ['title' => 'Check in again', 'detail' => 'Notice the difference after a few minutes. Finish there, or choose another clip if it suits you today.'],
];

$breathSession = [
    ['title' => 'Get comfortable', 'detail' => 'Choose standing, seated or hands-and-knees practice. Let your shoulders soften before starting.'],
    ['title' => 'Notice your breath', 'detail' => 'Follow a short lesson and notice your ribs moving. Keep breathing naturally without forcing a deeper breath.'],
    ['title' => 'Take it with you', 'detail' => 'Return to your normal rhythm. Revisit the same position or bring a brief breath check into your next class.'],
];

return [
    'defaults' => [
        'eyebrow' => 'Make room for movement',
        'focus' => 'Move at your pace',
        'image' => 'movement-side-reach.webp',
        'movement' => 'side-reach',
        'expect' => 'Browse the published classes below and choose a starting point that matches your experience, energy and advice from your care team.',
        'learn' => ['Find a comfortable starting position.', 'Practise a little at a time, with room to pause.', 'Notice how you feel during movement and afterwards.'],
        'structure' => [
            ['title' => 'Check in', 'detail' => 'Make space, gather your equipment and notice your energy before pressing play.'],
            ['title' => 'Practise', 'detail' => 'Follow the class at your own pace. Pause or finish early whenever you need.'],
            ['title' => 'Reflect', 'detail' => 'Take a quiet moment afterwards and choose what you would like to revisit.'],
        ],
        'adaptations' => ['Choose a shorter session on a lower-energy day.', 'Keep a stable chair nearby if you need support.'],
        'equipment_note' => 'Set up on a clear, non-slip surface with water within reach.',
    ],
    'courses' => [
        'bump-strength-t1' => [
            'structure' => $strengthSession,
            'eyebrow' => 'A gentle beginning',
            'focus' => 'Foundations & breath',
            'image' => 'movement-side-reach.webp',
            'movement' => 'side-reach',
            'expect' => 'Two strength classes per week, organised around the early-pregnancy themes below. Begin with breath, then explore familiar squat, hinge and posture patterns without chasing a personal best.',
            'learn' => ['Pair an easy breath rhythm with strength movements.', 'Explore squat and hinge patterns with a comfortable range.', 'Build a repeatable routine around changing energy.'],
            'adaptations' => ['Use bodyweight or reduce the load while learning a pattern.', 'Try a shorter section when fatigue or nausea makes a full class unappealing.', 'Use a chair for support and allow time between position changes.'],
            'equipment_note' => 'Have the listed equipment ready; start with a load you can control comfortably.',
        ],
        'bump-strength-t2' => [
            'structure' => $strengthSession,
            'eyebrow' => 'Strength for a changing body',
            'focus' => 'Legs, posture & stability',
            'image' => 'movement-calf.webp',
            'movement' => 'calf-stretch',
            'expect' => 'A week-by-week path through full-body strength, lower-body work, upper-body posture and balance. The three curriculum phases let you revisit foundations before moving into steady practice.',
            'learn' => ['Practise squat, hinge and carry patterns.', 'Explore upper-back and glute strength alongside balance work.', 'Adjust your stance, support and pace as your bump grows.'],
            'adaptations' => ['Use a chair or wall when balance feels different.', 'Make room for your bump rather than forcing a fixed stance.', 'Choose standing, seated or side-lying alternatives to extended back-lying.'],
        ],
        'bump-strength-t3' => [
            'structure' => $strengthSession,
            'eyebrow' => 'Steady through the final stretch',
            'focus' => 'Support, stamina & ease',
            'image' => 'movement-cat.webp',
            'movement' => 'cat-neutral',
            'expect' => 'Strength and mobility themes for later pregnancy, moving from grounding to sustained practice and gentler sessions. Your calendar is a guide; comfort and your care team’s advice come first.',
            'learn' => ['Explore supported strength with your changing centre of gravity.', 'Practise a steady breath during effort and recovery.', 'Build a collection of comfortable positions to return to.'],
            'adaptations' => ['Keep support close and shorten a movement when needed.', 'Take more recovery time and skip a round if you need to.', 'Use seated or side-lying positions in place of prolonged back-lying.'],
        ],
        'postnatal-rebuild' => [
            'structure' => $rebuildSession,
            'eyebrow' => 'Reconnect, then rebuild',
            'focus' => 'Breath, core & everyday strength',
            'image' => 'movement-cat.webp',
            'movement' => 'cat-neutral',
            'expect' => 'Eight curriculum weeks and sixteen classes, beginning with breath and core awareness before standing strength and carrying themes. These are course weeks, not a deadline after birth: repeat a week or pause as needed.',
            'learn' => ['Reconnect breath with gentle core practice.', 'Explore standing and carrying patterns for everyday life.', 'Build consistency before adding more challenge.'],
            'adaptations' => ['Begin with the reconnect classes and repeat them when useful.', 'Reduce load, range or session length rather than rushing the next week.', 'Pause for feeds, rest or baby care; the class will wait.'],
            'equipment_note' => 'A pillow can stand in for the small Pilates ball. Keep weights aside until you are ready to use them.',
        ],
        'mom-baby-flow' => [
            'structure' => $rebuildSession,
            'eyebrow' => 'A little time, together',
            'focus' => 'Connection & everyday movement',
            'image' => 'movement-side-reach.webp',
            'movement' => 'side-reach',
            'expect' => 'A pick-and-mix collection with floor-play, posture and strength themes. Choose a time when you and your baby are settled; interruptions are part of the session.',
            'learn' => ['Build short movement breaks around your day.', 'Explore posture and lower-body strength themes.', 'Create a routine that leaves room for baby care.'],
            'adaptations' => ['You can do the movement with baby beside you; baby does not need to be used as a weight.', 'Keep baby in a separate clear, supervised space away from equipment.', 'Skip any carrying or sling movement unless you have appropriate individual guidance.'],
            'equipment_note' => 'Prepare your mat and baby’s blanket before starting. Keep bands and weights out of baby’s reach.',
        ],
        'mobility-studio' => [
            'structure' => $librarySession,
            'eyebrow' => 'Find a little more space',
            'focus' => 'Hips, spine & shoulders',
            'image' => 'movement-cat.webp',
            'movement' => 'cat-neutral',
            'expect' => 'Ten stand-alone sessions organised around different body areas, from morning movement to an evening wind-down. Choose the area you want to explore rather than following a weekly plan.',
            'learn' => ['Explore hip and spine movement with control.', 'Notice how posture changes the feel of a reach.', 'Choose a focused session for shoulders, feet or the whole body.'],
            'adaptations' => ['Use a block or firm support to make a position more comfortable.', 'Explore a small range before going further.', 'Choose a seated option when getting to the floor is awkward.'],
        ],
        'breath-core-foundations' => [
            'structure' => $breathSession,
            'eyebrow' => 'Start with a breath',
            'focus' => 'Ribs, breath & body awareness',
            'image' => 'movement-side-reach.webp',
            'movement' => 'side-reach',
            'expect' => 'Four short lessons explore breathing while standing, on hands and knees, lying down and seated. Start with the position that feels comfortable today; there is no need to complete all four at once.',
            'learn' => ['Notice the movement of your ribs as you breathe.', 'Compare breathing in different supported positions.', 'Make a short check-in part of your movement routine.'],
            'adaptations' => ['Keep the breath easy rather than trying to make every inhale bigger.', 'Choose seated or standing practice if floor work is uncomfortable.', 'In later pregnancy, choose seated or hands-and-knees practice instead of prolonged back-lying.'],
            'equipment_note' => 'No equipment is listed. A stable chair or a pillow may make your chosen position more comfortable.',
        ],
        'stretch-library' => [
            'structure' => $librarySession,
            'eyebrow' => 'A moment to unwind',
            'focus' => 'Targeted, comfortable stretches',
            'image' => 'movement-side-reach.webp',
            'movement' => 'side-reach',
            'expect' => 'Thirty short lessons pair seated and supported options for fifteen body areas. Pick one area for a brief break or add a comfortable stretch after another class.',
            'learn' => ['Find seated and supported options for the same area.', 'Explore the difference between a gentle stretch and forcing range.', 'Build your own short wind-down from the library.'],
            'adaptations' => ['Support your body with a chair or cushion when useful.', 'Ease out of a stretch that pinches or hurts.', 'Avoid pushing further simply because a joint feels more flexible.'],
        ],
        'warmup-library' => [
            'structure' => $librarySession,
            'eyebrow' => 'Get to know the movement',
            'focus' => 'Technique, one move at a time',
            'image' => 'movement-calf.webp',
            'movement' => 'calf-stretch',
            'expect' => 'Twenty-two individual movement clips, including marching, shoulder rolls, side reaches and heel raises. Use these as technique references and build up gradually before a longer class.',
            'learn' => ['Break a movement down before adding it to a class.', 'Explore standing, seated and floor-based warm-up patterns.', 'Choose a few familiar movements for an easy start.'],
            'adaptations' => ['Begin slowly and use a smaller movement range.', 'Use a wall or chair for balance.', 'Choose standing or seated clips if floor transitions do not suit you.'],
            'equipment_note' => 'Most clips need no equipment; the band pull-apart clip uses a resistance band.',
        ],
    ],
    'safety' => [
        'pregnancy' => [
            'title' => 'Your pregnancy, your starting point',
            'intro' => 'Discuss exercise with your midwife or doctor if you have a health condition, pregnancy complications or concerns. A course week is not personal medical clearance.',
            'points' => [
                'Start gently. Keep moderate effort conversational, drink water and keep cool. Stop if you feel uncomfortable, too hot or unwell.',
                'In later pregnancy, avoid lying flat on your back for more than a few minutes; choose a seated or side-lying alternative.',
                'Stop exercise and contact your maternity team immediately for vaginal bleeding, tummy or calf pain, difficulty walking, or a persistent severe headache.',
            ],
            'sources' => ['nhs_pregnancy', 'pogp'],
        ],
        'postpartum' => [
            'title' => 'Recovery has its own pace',
            'intro' => 'A “from 6 weeks” course label is not automatic clearance. Your birth, healing and symptoms matter; ask your care team what is appropriate for you.',
            'points' => [
                'After a straightforward birth, gentle movement can begin when you feel ready. Build up gradually; get advice before high-impact exercise.',
                'After a caesarean or a complicated birth, speak to your midwife or doctor before strenuous exercise.',
                'Seek help from your GP or a pelvic health physiotherapist for leakage, ongoing pelvic or back pain, or concerns about abdominal separation.',
            ],
            'sources' => ['nhs_postpartum', 'pogp'],
        ],
        'any' => [
            'title' => 'Choose the version that fits you',
            'intro' => 'An “all stages” library offers choices; individual movements may not suit every pregnancy or recovery. Follow your care team’s advice.',
            'points' => [
                'Begin gently and stop if you feel uncomfortable or unwell. In later pregnancy, replace prolonged flat back-lying with another position.',
                'After a caesarean or complicated birth, ask your care team before strenuous activity. Build up gradually after birth.',
                'Ask a pelvic health physiotherapist or your doctor about ongoing pain, leakage or concerns about abdominal separation.',
            ],
            'sources' => ['nhs_pregnancy', 'nhs_postpartum', 'pogp'],
        ],
    ],
    'sources' => [
        'nhs_pregnancy' => ['label' => 'NHS · Exercise in pregnancy', 'url' => 'https://www.nhs.uk/pregnancy/keeping-well/exercise/'],
        'nhs_postpartum' => ['label' => 'NHS · Exercise after birth', 'url' => 'https://www.nhs.uk/baby/support-and-services/keeping-fit-and-healthy-with-a-baby/'],
        'pogp' => ['label' => 'POGP · Exercise in the childbearing years', 'url' => 'https://thepogp.co.uk/patient_information/womens_health/advice_guidance_for_exercise_in_the_childbearing_years.aspx'],
    ],
];
