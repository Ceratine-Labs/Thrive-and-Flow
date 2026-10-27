@extends('layouts.base')

@section('title', $lesson->title)

@section('body')
<div class="player">
  <div class="wrap" style="padding-top:18px">
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:14px">
      <p class="crumb" style="margin-bottom:0">
        @auth
          @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}">Studio Manager</a> /
          @else
            <a href="{{ route('studio.dashboard') }}">My studio</a> /
          @endif
        @else
          <a href="{{ route('home') }}">Thrive &amp; Flow</a> /
        @endauth
        <a href="{{ route('programs.show', $program) }}">{{ $program->title }}</a>
        @if ($lesson->week) / <span class="here">{{ $lesson->week->label() }}</span>@endif
      </p>
      @guest
        <a class="btn onink sm" style="margin-left:auto" href="{{ route('register') }}">Start your free week</a>
      @endguest
    </div>

    <div class="player-grid">
      <div>
        <div class="video">
          @if ($lesson->video_provider === 'youtube' && $lesson->video_ref)
            <iframe src="https://www.youtube-nocookie.com/embed/{{ $lesson->video_ref }}" title="{{ $lesson->title }}"
                    allow="accelerometer; autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
          @elseif ($lesson->video_provider === 'vimeo' && $lesson->video_ref)
            <iframe src="https://player.vimeo.com/video/{{ $lesson->video_ref }}" title="{{ $lesson->title }}"
                    allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
          @elseif ($lesson->video_provider === 'bunny' && $lesson->video_ref)
            <iframe src="https://iframe.mediadelivery.net/embed/{{ $lesson->video_ref }}" title="{{ $lesson->title }}"
                    allow="accelerometer; autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
          @elseif ($lesson->video_provider === 'file' && $lesson->video_ref)
            <video controls preload="metadata" src="{{ asset($lesson->video_ref) }}"></video>
          @else
            <div class="vbreath" aria-hidden="true"><div></div><div></div><div></div></div>
            <span class="playbtn" aria-hidden="true"><svg><use href="#i-play"/></svg></span>
            <span class="vnote">Video lands here — attach it in the Studio Manager</span>
          @endif
        </div>

        <div class="les-head">
          <div>
            <h1>{{ $lesson->title }}</h1>
            <div class="vchips">
              @if ($lesson->durationLabel())<span class="vchip num">{{ $lesson->durationLabel() }}</span>@endif
              @if ($lesson->week)<span class="vchip">{{ $lesson->week->label() }} · {{ $lesson->week->theme }}</span>@endif
              <span class="vchip">{{ $program->level ?? 'All levels' }}</span>
              @if ($lesson->is_free_preview)<span class="vchip" style="color:#F0CFC9;border-color:rgba(231,196,192,.5)">Free preview</span>@endif
            </div>
          </div>
          @auth
            <div class="acts">
              <form method="POST" action="{{ route('studio.complete', $lesson) }}">
                @csrf
                <button class="btn onink sm"><svg width="13" height="13"><use href="#i-check"/></svg>
                  {{ $progress?->completed_at ? 'Done ✓ — undo' : 'Mark complete' }}</button>
              </form>
              <form method="POST" action="{{ route('studio.favorite', $lesson) }}">
                @csrf
                <button class="vbtn {{ $isFavorite ? 'on' : '' }}" aria-label="Save class" title="{{ $isFavorite ? 'Saved' : 'Save for later' }}">
                  <svg><use href="#i-{{ $isFavorite ? 'heart-fill' : 'heart' }}"/></svg></button>
              </form>
            </div>
          @endauth
        </div>

        <div class="vtabs">
          <button class="vtab on" data-vt="about">About</button>
          @if ($lesson->modifications)<button class="vtab" data-vt="mods">Modifications</button>@endif
        </div>
        <div class="vpane on" id="vt-about">
          <p>{{ $lesson->description ?? 'A guided session from the '.$program->title.' collection.' }}</p>
        </div>
        @if ($lesson->modifications)
          <div class="vpane" id="vt-mods">
            @foreach (preg_split('/\r?\n/', $lesson->modifications) as $line)
              @if (trim($line) !== '')
                @php [$tag, $rest] = array_pad(explode('—', $line, 2), 2, null); @endphp
                <div class="mod">
                  @if ($rest !== null)
                    <span class="mt">{{ trim($tag) }}</span>
                    <div><b>{{ trim($rest) }}</b></div>
                  @else
                    <div><b>{{ trim($line) }}</b></div>
                  @endif
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>

      <aside class="sidebar">
        <div class="sb-head">
          <h3>{{ $lesson->week?->label() ?? $program->title }}</h3>
          @auth<span>{{ $doneIds->count() }} of {{ $sidebar->count() }} done</span>@endauth
        </div>
        @foreach ($sidebar as $item)
          <a class="sb-item {{ $item->id === $lesson->id ? 'cur' : '' }} {{ isset($doneIds[$item->id]) ? 'done' : '' }} {{ $item->accessFor($user) !== 'ok' ? 'lock' : '' }}"
             href="{{ route('studio.watch', $item) }}">
            <span class="lp">
              @if (isset($doneIds[$item->id]))<svg style="width:10px;height:10px"><use href="#i-check"/></svg>
              @elseif ($item->accessFor($user) === 'ok')<svg><use href="#i-play"/></svg>
              @else<svg style="width:10px;height:10px"><use href="#i-lock"/></svg>@endif
            </span>
            <b>{{ $item->title }}</b>
            <span class="lm num">{{ $item->durationLabel() }}</span>
          </a>
        @endforeach

        @if ($nextWeek && $nextWeekLessons->isNotEmpty())
          <div class="sb-sec">{{ $nextWeek->label() }} · {{ $nextWeek->theme }}</div>
          @foreach ($nextWeekLessons as $item)
            <a class="sb-item {{ $item->accessFor($user) !== 'ok' ? 'lock' : '' }}" href="{{ route('studio.watch', $item) }}">
              <span class="lp">
                @if ($item->accessFor($user) === 'ok')<svg><use href="#i-play"/></svg>
                @else<svg style="width:10px;height:10px"><use href="#i-lock"/></svg>@endif
              </span>
              <b>{{ $item->title }}</b>
              <span class="lm num">{{ $item->durationLabel() }}</span>
            </a>
          @endforeach
        @endif

        @if ($next && $next->id !== $lesson->id)
          <div class="upnext"><span>Up next</span>
            <a class="btn ghost-dark xs" href="{{ route('studio.watch', $next) }}">{{ \Illuminate\Support\Str::limit($next->title, 26) }} →</a>
          </div>
        @endif
      </aside>
    </div>
  </div>
</div>
@endsection
