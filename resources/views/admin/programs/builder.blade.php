@extends('layouts.admin')

@section('title', $program->title.' · builder')

@section('content')
<div class="adm-top">
  <div>
    <p class="crumb" style="margin-bottom:6px"><a href="{{ route('admin.programs.index') }}">Programs</a> / <span class="here">Builder</span></p>
    <h1>{{ $program->title }}</h1>
  </div>
  <span class="tstat {{ $program->is_published ? 'good' : 'dim' }}">{{ $program->is_published ? 'Published' : 'Hidden' }}</span>
  <form method="POST" action="{{ route('admin.programs.publish', $program) }}">
    @csrf
    <button class="sw {{ $program->is_published ? 'on' : '' }}" role="switch"
            aria-checked="{{ $program->is_published ? 'true' : 'false' }}" aria-label="Toggle published"></button>
  </form>
  <a class="btn ghost sm right" href="{{ route('programs.show', $program) }}">View as member</a>
</div>

<details class="chart-card" style="margin-bottom:16px" {{ $errors->hasAny(['title', 'stage']) ? 'open' : '' }}>
  <summary style="cursor:pointer;font-weight:600;font-size:13px;color:var(--mut)">Program settings — title, stage, cover, drip</summary>
  <form method="POST" action="{{ route('admin.programs.update', $program) }}" style="margin-top:16px">
    @csrf @method('PUT')
    @include('admin.programs._fields', ['program' => $program])
    <button class="btn dark sm">Save settings</button>
  </form>
</details>

@if ($program->kind->value === 'program')
  @foreach ($program->weeks as $week)
    <div class="b-week">
      <span><span class="wn">{{ $week->label() }}</span><br><span class="wt">{{ $week->theme }}</span></span>
      @if ($week->phase)<span class="chip dim">{{ $week->phase }}</span>@endif
      <span class="right tiny muted num">{{ $week->lessons->count() }} classes</span>
      <details style="position:relative">
        <summary class="btn ghost xs" style="list-style:none">Edit week</summary>
        <form method="POST" action="{{ route('admin.weeks.update', $week) }}" class="frow" style="margin-top:10px">
          @csrf @method('PUT')
          <div class="field"><label>Theme</label><input class="input" name="theme" value="{{ $week->theme }}" required></div>
          <div class="field"><label>Phase label</label><input class="input" name="phase" value="{{ $week->phase }}"></div>
          <button class="btn dark xs">Save</button>
        </form>
        @if ($week->lessons->isEmpty())
          <form method="POST" action="{{ route('admin.weeks.destroy', $week) }}" style="margin-top:8px"
                onsubmit="return confirm('Remove {{ $week->label() }}?');">
            @csrf @method('DELETE')
            <button class="btn danger xs">Remove week</button>
          </form>
        @endif
      </details>
    </div>
    @foreach ($week->lessons->sortBy('position') as $lesson)
      @include('admin.programs._lesson', ['lesson' => $lesson, 'program' => $program])
    @endforeach
  @endforeach

  @php $orphans = $program->lessons->whereNull('program_week_id'); @endphp
  @if ($orphans->isNotEmpty())
    <div class="b-week"><span class="wt">Unassigned classes</span></div>
    @foreach ($orphans->sortBy('position') as $lesson)
      @include('admin.programs._lesson', ['lesson' => $lesson, 'program' => $program])
    @endforeach
  @endif
@else
  <div class="b-week"><span class="wt">Library classes</span>
    <span class="right tiny muted num">{{ $program->lessons->count() }} classes</span></div>
  @foreach ($program->lessons->sortBy('position') as $lesson)
    @include('admin.programs._lesson', ['lesson' => $lesson, 'program' => $program])
  @endforeach
@endif

<div class="grid2" style="margin-top:20px;align-items:start">
  @if ($program->kind->value === 'program')
    <div class="addform">
      <h4>Add a week</h4>
      <form method="POST" action="{{ route('admin.weeks.store', $program) }}" class="frow">
        @csrf
        <div class="field slim"><label>Week no.</label>
          <input class="input num" type="number" name="week_number" min="1" max="42" required></div>
        <div class="field"><label>Theme</label>
          <input class="input" name="theme" placeholder="e.g. Steady strength" required></div>
        <div class="field"><label>Phase label <span style="font-weight:400;color:var(--faint)">optional</span></label>
          <input class="input" name="phase" placeholder="e.g. Build · weeks 18–22"></div>
        <button class="btn dark sm">Add week</button>
      </form>
    </div>
  @endif
  <div class="addform">
    <h4>Add a class</h4>
    <form method="POST" action="{{ route('admin.lessons.store', $program) }}">
      @csrf
      <div class="frow">
        <div class="field" style="flex:2 1 200px"><label>Title</label>
          <input class="input" name="title" placeholder="Class 1 — Full-body strength" required></div>
        @if ($program->weeks->isNotEmpty())
          <div class="field slim"><label>Week</label>
            <select class="input" name="program_week_id">
              <option value="">—</option>
              @foreach ($program->weeks as $w)
                <option value="{{ $w->id }}">{{ $w->label() }}</option>
              @endforeach
            </select></div>
        @endif
        <div class="field slim"><label>Length</label>
          <input class="input num" name="duration" placeholder="24:10" pattern="\d{1,3}:\d{2}"></div>
      </div>
      <div class="frow" style="margin-top:12px">
        <div class="field slim"><label>Video host</label>
          <select class="input" name="video_provider">
            <option value="">None yet</option>
            <option value="bunny">Bunny Stream</option>
            <option value="youtube">YouTube (unlisted)</option>
            <option value="vimeo">Vimeo</option>
          </select></div>
        <div class="field"><label>Video ID / reference</label>
          <input class="input" name="video_ref" placeholder="e.g. 4471/8f2c… or dQw4w9WgXcQ"></div>
        <button class="btn dark sm">Add class</button>
      </div>
    </form>
  </div>
</div>
@endsection
