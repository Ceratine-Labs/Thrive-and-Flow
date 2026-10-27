@extends('layouts.site')

@section('title', $program->title)

@php
  use App\Enums\ProgramKind;

  $allLessons = $program->kind === ProgramKind::Program
      ? $program->weeks->flatMap->lessons
      : $program->lessons;
  $count = $allLessons->count();
  $avg = $count ? (int) round($allLessons->avg('duration_seconds') / 60) : null;
  $preview = $allLessons->firstWhere('is_free_preview', true);
  $startLesson = $preview ?? $allLessons->first();
  $displayLevel = str_starts_with(strtolower($program->level ?? ''), 'from 6 weeks') ? 'Postnatal · readiness varies' : ($program->level ?? 'All levels');
@endphp

@section('content')
<div class="wrap cg-detail">
  <div class="phero">
    <div>
      <p class="crumb"><a href="{{ route('programs.index') }}">Programs</a> / {{ $program->stage->label() }} /
        <span class="here">{{ $program->title }}</span></p>
      <p class="eyebrow cg-hero-eyebrow">{{ $guide['eyebrow'] }}</p>
      <h1>{{ $program->title }}</h1>
      <p class="lede">{{ $program->description ?? $program->subtitle }}</p>
      <div class="hero-ctas">
        @if ($startLesson)
          <a class="btn primary" href="{{ route('studio.watch', $startLesson) }}">
            {{ $user?->hasStudioAccess() ? 'Start a class' : ($preview ? 'Watch the free preview' : 'View the first class') }}
            <svg class="ar" width="15" height="15"><use href="#i-arrow"/></svg></a>
        @endif
        @guest
          <a class="btn ghost" href="{{ route('register') }}">Start your free week</a>
        @endguest
      </div>
      <a class="cg-text-link" href="#course-guide">Get to know this course <span aria-hidden="true">↓</span></a>
      @if ($safety)
        <p class="cg-before-start">Before your first class, <a href="#course-safety">read the movement guidance</a> and follow your care team’s advice.</p>
      @endif
    </div>
    <div class="pposter cg-detail-poster {{ $program->color }}">
      <span class="cg-poster-focus">{{ $guide['focus'] }}</span>
      <img src="{{ asset('images/'.$guide['image']) }}" alt="" width="600" height="450" class="cg-poster-art">
      <span class="cg-poster-caption">A little movement. Room to breathe.</span>
      @if ($preview)
        <a class="playbtn" href="{{ route('studio.watch', $preview) }}" aria-label="Play preview"><svg><use href="#i-play"/></svg></a>
        <span class="weekchip">{{ $preview->week?->label() ? $preview->week->label().' · ' : '' }}{{ $preview->title }}@if($preview->is_free_preview) — free preview @endif</span>
      @endif
    </div>
  </div>

  <div class="facts">
    <div class="fact"><b>{{ $program->weeksLabel() }}</b><span>{{ $program->stage->label() }}</span></div>
    <div class="fact"><b class="num">{{ $count }} classes</b><span>@if($program->classes_per_week){{ $program->classes_per_week }} per week @else In the library @endif</span></div>
    @if ($avg)<div class="fact"><b>~{{ $avg }} min</b><span>Average class</span></div>@endif
    <div class="fact"><b>{{ $displayLevel }}</b><span>Choose your pace</span></div>
    @if ($program->drip_by_week)<div class="fact"><b>Week by week</b><span>Unlocks with you</span></div>@endif
  </div>

  <section class="cg-guide" id="course-guide" aria-labelledby="course-guide-title">
    <div class="cg-guide-intro">
      <p class="eyebrow">Before you press play</p>
      <h2 id="course-guide-title">What to expect</h2>
      <p>{{ $guide['expect'] }}</p>
    </div>
    <div class="cg-learn">
      <h3>What you’ll practise</h3>
      <ul>@foreach ($guide['learn'] as $item)<li>{{ $item }}</li>@endforeach</ul>
    </div>
  </section>

  <section class="cg-session" aria-labelledby="session-title">
    <div class="cg-session-head">
      <p class="eyebrow">Make it your own</p>
      <h2 id="session-title">A simple session rhythm</h2>
      <p>A suggested way to approach a class. Follow the lesson’s own sequence and take the breaks you need.</p>
    </div>
    <ol class="cg-session-steps">
      @foreach ($guide['structure'] as $step)
        <li><span aria-hidden="true">0{{ $loop->iteration }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['detail'] }}</p></li>
      @endforeach
    </ol>
  </section>

  <div class="pd-grid">
    <div>
      <p class="eyebrow">{{ $program->kind === ProgramKind::Program ? 'Curriculum' : 'The classes' }}</p>
      <h2 style="font-size:clamp(23px,2.9vw,30px);margin-bottom:16px">
        {{ $program->kind === ProgramKind::Program ? 'Week by week' : 'Pick any class, any day' }}</h2>

      @if ($program->kind === ProgramKind::Program)
        @php $lastPhase = null; @endphp
        @foreach ($program->weeks as $week)
          @if ($week->phase && $week->phase !== $lastPhase)
            <div class="phase">{{ $week->phase }}</div>
            @php $lastPhase = $week->phase; @endphp
          @endif
          @php
            $weekDone = $week->lessons->every(fn ($l) => isset($completed[$l->id]));
            $mins = (int) round($week->lessons->sum('duration_seconds') / 60);
          @endphp
          <div class="wk {{ $loop->first ? 'open' : '' }}">
            <button class="wk-head" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
              <span class="wk-n">{{ $week->label() }}</span>
              <span class="wk-t">{{ $week->theme }}</span>
              <span class="wk-meta">{{ $week->lessons->count() }} classes · ~{{ $mins }} min @if($weekDone && $completed->isNotEmpty()) · done ✓ @endif</span>
              <svg class="wk-car"><use href="#i-chev"/></svg>
            </button>
            <div class="wk-body">
              @foreach ($week->lessons as $lesson)
                @php $access = $lesson->accessFor($user); @endphp
                <a class="les {{ isset($completed[$lesson->id]) ? 'done' : '' }} {{ $access !== 'ok' ? 'lock' : '' }}"
                   href="{{ route('studio.watch', $lesson) }}">
                  <span class="lp">
                    @if (isset($completed[$lesson->id]))<svg width="11" height="11"><use href="#i-check"/></svg>
                    @elseif ($access === 'ok')<svg><use href="#i-play"/></svg>
                    @else<svg width="11" height="11"><use href="#i-lock"/></svg>@endif
                  </span>
                  <b>{{ $lesson->title }}</b>
                  @if ($lesson->is_free_preview)<span class="free">Free preview</span>@endif
                  @if ($access === 'drip')<span class="chip dim" style="font-size:10px">unlocks week {{ $week->week_number }}</span>@endif
                  <span class="lm num">{{ $lesson->durationLabel() }}</span>
                </a>
              @endforeach
            </div>
          </div>
        @endforeach
      @else
        <div class="wk open"><div class="wk-body" style="border-top:none;padding-top:11px">
          @foreach ($program->lessons as $lesson)
            @php $access = $lesson->accessFor($user); @endphp
            <a class="les {{ isset($completed[$lesson->id]) ? 'done' : '' }} {{ $access !== 'ok' ? 'lock' : '' }}"
               href="{{ route('studio.watch', $lesson) }}">
              <span class="lp">
                @if (isset($completed[$lesson->id]))<svg width="11" height="11"><use href="#i-check"/></svg>
                @elseif ($access === 'ok')<svg><use href="#i-play"/></svg>
                @else<svg width="11" height="11"><use href="#i-lock"/></svg>@endif
              </span>
              <b>{{ $lesson->title }}</b>
              @if ($lesson->is_free_preview)<span class="free">Free preview</span>@endif
              <span class="lm num">{{ $lesson->durationLabel() }}</span>
            </a>
          @endforeach
        </div></div>
      @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
      <div class="cg-adapt-card">
        <p class="eyebrow">Room to adapt</p>
        <h3>Let today set the pace.</h3>
        <ul>@foreach ($guide['adaptations'] as $item)<li>{{ $item }}</li>@endforeach</ul>
      </div>
      @if ($program->equipment)
        <div class="card" style="padding:20px 22px">
          <h3 class="side-h">What you'll need</h3>
          <div class="equip">
            @foreach ($program->equipment as $item)
              @php
                $l = strtolower($item);
                $eqIcon = str_contains($l, 'mat') ? 'mat' : (str_contains($l, 'ball') ? 'ball'
                  : (str_contains($l, 'dumb') ? 'dumb' : (str_contains($l, 'band') ? 'wave' : 'check')));
              @endphp
              <span class="eq"><svg><use href="#i-{{ $eqIcon }}"/></svg>{{ $item }}</span>
            @endforeach
          </div>
          <p class="cg-equipment-note">{{ $guide['equipment_note'] }}</p>
        </div>
      @endif
      <a class="cg-movement-link" href="{{ route('home', ['movement' => $guide['movement']]) }}#movement">
        <span class="eyebrow">See how it moves</span>
        <h3>Your movement companion <span aria-hidden="true">↗</span></h3>
        <p>Explore illustrated movements, body focus and comfortable alternatives.</p>
      </a>

    </div>
  </div>
  @if ($safety)
    <section class="cg-safety" id="course-safety" aria-labelledby="course-safety-title">
      <div>
        <p class="eyebrow">Move with care</p>
        <h2 id="course-safety-title">{{ $safety['title'] }}</h2>
        <p>{{ $safety['intro'] }}</p>
        <p class="cg-safety-disclaimer">General education to help you prepare; individual advice comes from your care team.</p>
      </div>
      <div>
        <ul>@foreach ($safety['points'] as $point)<li>{{ $point }}</li>@endforeach</ul>
        <div class="cg-sources">
          <b>Read the guidance</b>
          @foreach ($safety['sources'] as $sourceKey)
            @php $source = config('course_guides.sources.'.$sourceKey); @endphp
            <a href="{{ $source['url'] }}">{{ $source['label'] }} <span aria-hidden="true">↗</span></a>
          @endforeach
        </div>
      </div>
    </section>
  @endif
</div>
@endsection
