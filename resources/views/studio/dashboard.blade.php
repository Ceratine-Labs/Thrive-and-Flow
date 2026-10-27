@extends('layouts.studio')

@section('title', 'Your studio')

@section('streak')
  @if ($streak > 0)
    <span class="streak"><svg><use href="#i-flame"/></svg>{{ $streak }}-day streak</span>
  @endif
@endsection

@section('content')
<div class="wrap">
  <div class="dash-hero">
    <div>
      <p class="eyebrow">{{ now()->format('l') }} · your week at a glance</p>
      <h1>{{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }}, <i>{{ $user->firstName() }}</i></h1>
      <p class="sub">
        @if ($subscription?->isTrial())
          Your free week is open until {{ $subscription->trial_ends_at->format('l j M') }} — everything's unlocked, wander freely.
        @elseif ($user->stageLabel())
          You're at {{ $user->stageLabel() }} — the studio is matched to exactly where you are.
        @else
          Pick any class below — the studio is yours.
        @endif
      </p>
    </div>
    @if ($week = $user->pregnancyWeek())
      <div class="ringwrap">
        <div class="ring" style="background:conic-gradient(var(--slate) 0 {{ (int) round($week / 40 * 100) }}%, var(--wash) {{ (int) round($week / 40 * 100) }}% 100%)">
          <i></i><span class="rv"><b class="num">W{{ $week }}</b><span>of 40</span></span>
        </div>
        <div class="ringinfo"><b>{{ $user->stageLabel() }}</b>
          <span>@if ($week < 28) Trimester {{ $week <= 13 ? 2 : 3 }} begins at week {{ $week <= 13 ? 14 : 28 }} — your plan shifts with you. @else The finish line is close — gentler weeks ahead. @endif</span>
        </div>
      </div>
    @elseif ($week = $user->postpartumWeek())
      <div class="ringwrap">
        <div class="ring" style="background:conic-gradient(var(--blush-deep) 0 {{ (int) min(100, round($week / 12 * 100)) }}%, var(--wash) {{ (int) min(100, round($week / 12 * 100)) }}% 100%)">
          <i></i><span class="rv"><b class="num">W{{ $week }}</b><span>postpartum</span></span>
        </div>
        <div class="ringinfo"><b>{{ $user->stageLabel() }}</b>
          <span>Rebuild at your own pace — every class waits for you.</span></div>
      </div>
    @endif
  </div>

  @if (! $subscription && ! $user->isAdmin())
    <div class="subnote" style="margin-top:14px">Your membership isn't active right now —
      <a href="{{ route('pricing') }}" style="font-weight:600;color:var(--rose-ink);text-decoration:underline">restart it here</a>
      to unlock the whole studio. Your progress is safe.</div>
  @endif

  <div class="dash-grid">
    <div class="dcol">
      @if ($continueRow)
        @php
          $cl = $continueRow->lesson;
          $pct = $cl->duration_seconds ? (int) min(99, round($continueRow->seconds_watched / $cl->duration_seconds * 100)) : 0;
          $left = $cl->duration_seconds ? (int) max(1, round(($cl->duration_seconds - $continueRow->seconds_watched) / 60)) : null;
        @endphp
        <div class="card panelcard">
          <h3>Continue where you paused</h3>
          <div class="cont">
            <span class="cont-poster">
              <a class="playbtn" href="{{ route('studio.watch', $cl) }}" aria-label="Resume class"><svg><use href="#i-play"/></svg></a>
              <span class="dur">{{ $cl->durationLabel() }}</span>
            </span>
            <span class="cont-info">
              <p class="crumb" style="margin-bottom:5px">{{ $cl->program->title }}{{ $cl->week ? ' · '.$cl->week->label() : '' }}</p>
              <h4>{{ $cl->title }}</h4>
              <span class="cont-bar"><i style="width:{{ max(4, $pct) }}%"></i></span>
              <span class="left">@if($left){{ $left }} min left · @endif picks up where you paused</span>
            </span>
          </div>
        </div>
      @endif

      <div class="card panelcard">
        <h3>{{ $planTitle }} <a href="{{ route('programs.index') }}">Browse all</a></h3>
        @forelse ($planLessons as $lesson)
          <a class="plan-row" href="{{ route('studio.watch', $lesson) }}">
            <span class="tick {{ isset($doneIds[$lesson->id]) ? 'done' : '' }}">
              @if (isset($doneIds[$lesson->id]))<svg><use href="#i-check"/></svg>@endif
            </span>
            <b>{{ $lesson->title }}</b>
            <span class="tiny muted">{{ $lesson->program->title }}</span>
            <span class="lm num">{{ $lesson->durationLabel() }}</span>
          </a>
        @empty
          <p class="tiny muted">Nothing scheduled — browse the studio and pick what feels good today.</p>
        @endforelse
      </div>

      @if ($myPrograms->isNotEmpty())
        <div class="card panelcard">
          <h3>My programs</h3>
          <div class="myprog">
            @foreach ($myPrograms as $row)
              <a class="mp" href="{{ route('programs.show', $row['program']) }}">
                <span class="mpc {{ $row['program']->color }}"><svg><use href="#i-{{ $row['program']->icon }}"/></svg></span>
                <div><b>{{ $row['program']->title }}</b>
                  <span class="mpbar"><i style="width:{{ $row['percent'] }}%"></i></span></div>
                <span class="mpp num">{{ $row['percent'] }}%</span>
              </a>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="dcol">
      <div class="card panelcard">
        <h3>Milestones</h3>
        <div class="mile"><span class="mic"><svg><use href="#i-trophy"/></svg></span>
          <span style="flex:1"><b>{{ $totalDone }} {{ $totalDone === 1 ? 'class' : 'classes' }} done</b><span>Quietly consistent — it shows</span></span></div>
        <div class="mile"><span class="mic blush"><svg><use href="#i-flame"/></svg></span>
          <span style="flex:1"><b>{{ $streak }}-day streak</b><span>{{ $streak > 0 ? 'Keep the thread going' : 'Press play today to start one' }}</span></span></div>
        @if ($subscription)
          <div class="mile"><span class="mic slate"><svg><use href="#i-shield"/></svg></span>
            <span style="flex:1"><b>{{ $subscription->isTrial() ? 'Free week' : $subscription->plan->name }}</b>
              <span>
                @if ($subscription->isTrial()) Trial ends {{ $subscription->trial_ends_at->format('j M') }}
                @elseif ($subscription->current_period_ends_at) Renews {{ $subscription->current_period_ends_at->format('j M') }}
                @else Active @endif
              </span></span></div>
        @endif
      </div>

      @if ($favorites->isNotEmpty())
        <div class="card panelcard">
          <h3>Saved for later</h3>
          @foreach ($favorites as $fav)
            <a class="plan-row" href="{{ route('studio.watch', $fav->lesson) }}">
              <span class="tick" style="border-color:var(--blush-deep);color:var(--rose-ink)"><svg style="width:10px;height:10px"><use href="#i-heart-fill"/></svg></span>
              <b>{{ $fav->lesson->title }}</b>
              <span class="lm num">{{ $fav->lesson->durationLabel() }}</span>
            </a>
          @endforeach
        </div>
      @endif

      @if ($subscription && ! $user->isAdmin())
        <div class="card panelcard">
          <h3>Membership</h3>
          <p class="tiny muted" style="margin-bottom:12px">{{ $subscription->plan->name }} · {{ $subscription->plan->priceLine() }}</p>
          <form method="POST" action="{{ route('studio.cancel') }}"
                onsubmit="return confirm('Cancel your membership? Your progress stays saved.');">
            @csrf
            <button class="btn ghost xs">Cancel membership</button>
          </form>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
