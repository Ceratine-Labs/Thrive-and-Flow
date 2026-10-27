@php
    $movements = config('movements');
    $firstMovement = reset($movements);
@endphp
<section class="movement-studio" id="movement" aria-labelledby="ms-heading" data-movement-studio>
  <div class="wrap">
    <div class="ms-heading-row">
      <div><p class="eyebrow">The movement library</p><h2 id="ms-heading">A closer look at <em>feeling good.</em></h2></div>
      <p>Get to know the movement before you move.<br> Explore the position, the pace and the body focus.</p>
    </div>

    <div class="ms-choices" role="group" aria-label="Choose a movement">
      @foreach($movements as $key => $movement)
        <button class="ms-choice {{ $loop->first ? 'is-selected' : '' }}" type="button" data-ms-choice="{{ $key }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" disabled>
          <span class="ms-choice-art"><img src="{{ asset($movement['image']) }}" alt="" width="120" height="120" loading="lazy"></span>
          <span class="ms-choice-copy"><span class="ms-micro">0{{ $loop->iteration }} / {{ $movement['category'] }}</span><strong>{{ $movement['title'] }}</strong><span>{{ $movement['short'] }}</span></span>
          <span class="ms-choice-arrow" aria-hidden="true">↗</span>
        </button>
      @endforeach
    </div>

    <div class="ms-workspace">
      <div class="ms-demonstration">
        <div class="ms-stage">
          <div class="ms-stage-top"><span class="ms-micro">Movement study <span data-ms-number>01</span> / 03</span><span class="ms-view" data-ms-view>{{ $firstMovement['view'] }}</span></div>
          <svg class="ms-pose" viewBox="0 0 500 390" role="img" aria-labelledby="ms-pose-title ms-pose-description">
            <title id="ms-pose-title">Seated side reach demonstration</title>
            <desc id="ms-pose-description">Illustration of a seated side reach. Play the demo or use the three step controls to explore the movement.</desc>
            <g data-ms-pose>
              <image href="{{ asset($firstMovement['image']) }}" width="500" height="390"/>
            </g>
          </svg>
          <div class="ms-stage-bottom"><span data-ms-focus-key><i aria-hidden="true"></i><span data-ms-focus-key-label>Illustrated movement guide</span></span><span>Small range. Your own pace.</span></div>
        </div>
        <div class="ms-controls" data-ms-controls hidden>
          <div class="ms-transport">
            <button type="button" class="ms-play" data-ms-play aria-label="Play movement demonstration"><span data-ms-play-icon aria-hidden="true">▶</span><span data-ms-play-label>Play demo</span></button>
            <button type="button" class="ms-replay" data-ms-replay aria-label="Replay movement demonstration"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 10a8 8 0 1 1 1.2 7M4 4v6h6"/></svg></button>
            <label class="ms-scrub"><span class="ms-sr-only">Demo position</span><input type="range" min="0" max="1000" value="0" step="1" data-ms-progress aria-valuetext="Set up"><span aria-hidden="true">Explore the movement</span></label>
            <span class="ms-state" data-ms-state>Paused</span>
          </div>
          <p class="ms-motion-note" data-ms-motion-note hidden>Reduced motion is on. Use the steps or slider to explore at your own pace.</p>
        </div>
      </div>

      <div class="ms-guide" aria-labelledby="ms-movement-title">
        <div class="ms-guide-head"><span class="ms-micro" data-ms-category>{{ $firstMovement['category'] }}</span><h3 id="ms-movement-title" data-ms-title>{{ $firstMovement['title'] }}</h3><span class="ms-equipment"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 4v9h10V8H5m1 5v4m8-4v4"/></svg><span data-ms-equipment>{{ $firstMovement['equipment'] }}</span></span></div>
        <ol class="ms-steps" data-ms-steps>
          @foreach($firstMovement['steps'] as $step)
            <li class="{{ $loop->first ? 'is-current' : '' }}"><button type="button" data-ms-step="{{ $loop->index }}" disabled {{ $loop->first ? 'aria-current=step' : '' }}><span class="ms-step-number">0{{ $loop->iteration }}</span><span><strong>{{ $step['title'] }}</strong><span>{{ $step['text'] }}</span></span></button></li>
          @endforeach
        </ol>
        <button class="ms-detail-button" type="button" data-ms-open hidden>Open full movement guide <span aria-hidden="true">↗</span></button>
      </div>
    </div>

    <div class="ms-focus-row">
      <div class="ms-body-map">
        <svg viewBox="0 0 120 190" role="img" aria-labelledby="ms-map-title ms-map-description" data-ms-body-map>
          <title id="ms-map-title">Body focus: side of the trunk</title><desc id="ms-map-description">Simplified front body map. Coral marks the side of the ribcage and trunk.</desc>
          <g fill="none" stroke="#b1b7ba" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"><path d="M43 60L27 99L23 120M77 60L93 99L97 120M47 113L43 148L40 176M73 113L77 148L80 176"/></g>
          <path d="M42 51Q60 43 78 51L80 112Q60 126 40 112Z" fill="#dbe0e1"/><ellipse cx="60" cy="27" rx="16" ry="19" fill="#dbe0e1"/><path d="M60 44V54" stroke="#dbe0e1" stroke-width="12"/>
          <g data-ms-highlight><path d="M75 60Q80 77 77 98" fill="none" stroke="#cb7a68" stroke-width="8" stroke-linecap="round"/><path d="M82 78H111" stroke="#cb7a68" stroke-width="1.5"/><circle cx="111" cy="78" r="3" fill="#cb7a68"/></g>
        </svg>
        <div><span class="ms-focus-label"><i aria-hidden="true"></i> Body focus <span class="ms-map-view" data-ms-map-view>{{ $firstMovement['map_view'] }}</span></span><h4 data-ms-focus>{{ $firstMovement['focus'] }}</h4><p data-ms-focus-detail>{{ $firstMovement['focus_detail'] }}</p></div>
      </div>
      <div class="ms-focus-explainer"><p>Highlights mark the movement focus.</p><span>Illustrative anatomy, not a pain map or a measurement of muscle activity. This guide cannot assess your individual movement or pregnancy.</span></div>
    </div>

    <details class="ms-safety">
      <summary><span><svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true"><path d="M10 2L17 5v5c0 4-7 8-7 8S3 14 3 10V5Z"/><path d="M10 6v5m0 2v1"/></svg>Before you try · guidance &amp; sources</span><span class="ms-summary-plus" aria-hidden="true">+</span></summary>
      <div class="ms-safety-content"><div><h4>Start with what suits you</h4><p>Discuss exercise with your maternity care team, especially if you have symptoms, a medical condition or pregnancy complications. These are educational examples, not an individual programme. Use a comfortable range, keep breathing and stop if anything feels uncomfortable.</p><p>Keep cool, have water nearby and avoid forcing or bouncing into a stretch. Demo speed is for observation; it does not prescribe repetitions or hold times.</p></div><div><h4>Know when to stop</h4><p>Stop and contact your maternity care team for bleeding, fluid leakage, painful regular contractions, dizziness, headache, chest pain, breathlessness before exercise, muscle weakness affecting balance, or calf pain or swelling.</p><p>For severe symptoms or an emergency, seek urgent medical help.</p></div><div class="ms-sources"><h4>Read the guidance</h4><ul>@foreach($movements as $movement)<li><a href="{{ $movement['source'] }}" target="_blank" rel="noopener noreferrer">{{ $movement['source_label'] }} <span aria-hidden="true">↗</span><span class="ms-sr-only"> (opens in a new tab)</span></a></li>@endforeach<li><a href="https://www.acog.org/womens-health/faqs/exercise-during-pregnancy" target="_blank" rel="noopener noreferrer">ACOG · exercise and warning signs <span aria-hidden="true">↗</span><span class="ms-sr-only"> (opens in a new tab)</span></a></li><li><a href="https://www.nhs.uk/pregnancy/keeping-well/exercise/" target="_blank" rel="noopener noreferrer">NHS · exercise in pregnancy <span aria-hidden="true">↗</span><span class="ms-sr-only"> (opens in a new tab)</span></a></li></ul><p>General guidance informs these examples; the sources have not reviewed or endorsed this website.</p></div></div>
    </details>

    <noscript><div class="ms-no-js"><p>The animated viewer needs JavaScript. All three written movement guides are available below.</p>@foreach($movements as $movement)<details><summary>{{ $movement['title'] }}</summary><ol>@foreach($movement['steps'] as $step)<li><strong>{{ $step['title'] }}.</strong> {{ $step['text'] }}</li>@endforeach</ol><p>{{ $movement['adaptation'] }}</p></details>@endforeach</div></noscript>
    <p class="ms-sr-only" aria-live="polite" aria-atomic="true" data-ms-announcement></p>
    <dialog class="ms-dialog" data-ms-dialog aria-labelledby="ms-dialog-title">
      <div class="ms-dialog-top"><span class="ms-micro">Your movement guide</span><button type="button" class="ms-dialog-close" data-ms-close aria-label="Close movement guide">×</button></div>
      <div class="ms-dialog-layout"><div class="ms-dialog-visual"><div class="ms-dialog-art"><img data-ms-dialog-image src="{{ asset($firstMovement['image']) }}" alt="Illustration of a seated side reach" width="480" height="480"></div><div class="ms-dialog-focus"><div data-ms-dialog-map aria-hidden="true"></div><div><span class="ms-focus-label">Body focus</span><h4 data-ms-dialog-focus></h4><p data-ms-dialog-focus-detail></p><p class="ms-dialog-map-note">Coral shows focus, not pain. Illustrative anatomy.</p></div></div></div><div><span class="ms-micro" data-ms-dialog-category></span><h3 id="ms-dialog-title" data-ms-dialog-title>{{ $firstMovement['title'] }}</h3><p class="ms-dialog-equipment" data-ms-dialog-equipment></p><ol data-ms-dialog-steps></ol><div class="ms-adaptation"><h4>Make room for your body</h4><p data-ms-dialog-adaptation></p></div><p class="ms-dialog-note">Keep the range comfortable and stop for discomfort. Your maternity team can help you choose movements that suit your pregnancy.</p><a class="ms-source-link" data-ms-dialog-source href="{{ $firstMovement['source'] }}" target="_blank" rel="noopener noreferrer">Read the movement source ↗<span class="ms-sr-only"> (opens in a new tab)</span></a></div></div>
    </dialog>
    <script type="application/json" data-ms-data>@json($movements)</script>
  </div>
</section>
