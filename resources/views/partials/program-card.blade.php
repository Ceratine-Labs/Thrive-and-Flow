@php
  /** @var \App\Models\Program $program */
  $stageAttr = $program->stage->value === 'any' ? 'pregnancy postpartum mom_baby any' : $program->stage->value;
  $count = $program->published_lessons_count ?? $program->lessons->where('is_published', true)->count();
  $cardLevel = str_starts_with(strtolower($program->level ?? ''), 'from 6 weeks') ? 'Postnatal · readiness varies' : ($program->level ?? 'Library');
  $cardHidden = isset($selectedStage) && $selectedStage !== 'all' && ! in_array($selectedStage, explode(' ', $stageAttr), true);
  $cardGuide = array_replace(config('course_guides.defaults'), config('course_guides.courses.'.$program->slug, []));
@endphp
<a class="pcard cg-card" href="{{ route('programs.show', $program) }}"
   data-stage="{{ $stageAttr }}" data-kind="{{ $program->kind->value }}" @if ($cardHidden) hidden @endif>
  <span class="pcover cg-cover {{ $program->color }}">
    <span class="cg-cover-orbit" aria-hidden="true"></span>
    <span class="cg-cover-label">{{ $cardGuide['focus'] }}</span>
    <img src="{{ asset('images/'.$cardGuide['image']) }}" alt="" loading="lazy" width="400" height="300">
    <span class="cg-cover-type">{{ $program->kind->value === 'program' ? 'A guided path' : 'Pick & mix' }}</span>
  </span>
  <span class="pbody">
    <span class="pmeta">
      <span class="chip {{ $program->stage->tint() }}">{{ $program->stage->label() }}</span>
      <span class="chip dim">{{ $program->kind->value === 'program' ? $program->weeksLabel() : $cardLevel }}</span>
    </span>
    <h3>{{ $program->title }}</h3>
    @if ($program->subtitle)
      <span class="pdesc">{{ $program->subtitle }}</span>
    @endif
    <span class="pfoot">
      <span>{{ $count }} {{ $count === 1 ? 'class' : 'classes' }}@if ($program->classes_per_week) · {{ $program->classes_per_week }}×/week @endif</span>
      <span class="go">Explore <span aria-hidden="true">↗</span></span>
    </span>
  </span>
</a>
