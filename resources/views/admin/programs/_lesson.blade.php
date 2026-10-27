@php $editing = (int) request('edit') === $lesson->id; @endphp
<div id="l{{ $lesson->id }}">
  <div class="b-row">
    <span class="bic {{ $lesson->is_published ? $program->color : 'dim' }}">
      <svg><use href="#i-{{ $lesson->video_ref ? 'video' : 'clock' }}"/></svg></span>
    <div>
      <b>{{ $lesson->title }}</b>
      <span class="bs">
        {{ $lesson->durationLabel() ?? '—:—' }}
        @if ($lesson->video_ref) · {{ $lesson->video_provider }} attached
        @else · <span style="color:var(--warn-ink)">no video yet</span>@endif
        @if ($lesson->is_free_preview) · free preview @endif
      </span>
    </div>
    <span class="bmeta">
      <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">
        @csrf <input type="hidden" name="dir" value="up">
        <button class="iconbtn" aria-label="Move up"><svg style="transform:rotate(180deg)"><use href="#i-chev"/></svg></button>
      </form>
      <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">
        @csrf <input type="hidden" name="dir" value="down">
        <button class="iconbtn" aria-label="Move down"><svg><use href="#i-chev"/></svg></button>
      </form>
      <form method="POST" action="{{ route('admin.lessons.preview', $lesson) }}">
        @csrf
        <button class="tstat {{ $lesson->is_free_preview ? 'blush' : 'dim' }}" title="Toggle free preview">Preview</button>
      </form>
      <form method="POST" action="{{ route('admin.lessons.publish', $lesson) }}">
        @csrf
        <button class="sw {{ $lesson->is_published ? 'on' : '' }}" role="switch"
                aria-checked="{{ $lesson->is_published ? 'true' : 'false' }}" aria-label="Toggle live"></button>
      </form>
      <a class="iconbtn" href="{{ route('admin.programs.builder', $program) }}?edit={{ $lesson->id }}#l{{ $lesson->id }}" aria-label="Edit class">
        <svg><use href="#i-cog"/></svg></a>
      <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}"
            onsubmit="return confirm('Delete “{{ $lesson->title }}”? Member progress on it is removed too.');">
        @csrf @method('DELETE')
        <button class="iconbtn" aria-label="Delete class"><svg><use href="#i-x"/></svg></button>
      </form>
    </span>
  </div>

  @if ($editing)
    <div class="addform" style="margin:2px 0 12px">
      <h4>Edit — {{ $lesson->title }}</h4>
      <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}">
        @csrf @method('PUT')
        <div class="frow">
          <div class="field" style="flex:2 1 200px"><label>Title</label>
            <input class="input" name="title" value="{{ old('title', $lesson->title) }}" required></div>
          @if ($program->weeks->isNotEmpty())
            <div class="field slim"><label>Week</label>
              <select class="input" name="program_week_id">
                <option value="">—</option>
                @foreach ($program->weeks as $w)
                  <option value="{{ $w->id }}" @selected($lesson->program_week_id === $w->id)>{{ $w->label() }}</option>
                @endforeach
              </select></div>
          @endif
          <div class="field slim"><label>Length</label>
            <input class="input num" name="duration" value="{{ $lesson->durationLabel() }}" pattern="\d{1,3}:\d{2}"></div>
          <div class="field slim"><label>Video host</label>
            <select class="input" name="video_provider">
              <option value="">None</option>
              @foreach (['bunny' => 'Bunny', 'youtube' => 'YouTube', 'vimeo' => 'Vimeo'] as $v => $label)
                <option value="{{ $v }}" @selected($lesson->video_provider === $v)>{{ $label }}</option>
              @endforeach
            </select></div>
          <div class="field"><label>Video ID</label>
            <input class="input" name="video_ref" value="{{ $lesson->video_ref }}"></div>
        </div>
        <div class="frow" style="margin-top:12px">
          <div class="field"><label>Description</label>
            <textarea class="input" name="description" style="min-height:70px">{{ $lesson->description }}</textarea></div>
          <div class="field"><label>Modifications <span style="font-weight:400;color:var(--faint)">one per line: Tag — cue</span></label>
            <textarea class="input" name="modifications" style="min-height:70px">{{ $lesson->modifications }}</textarea></div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px">
          <button class="btn dark sm">Save class</button>
          <a class="btn ghost sm" href="{{ route('admin.programs.builder', $program) }}">Close</a>
        </div>
      </form>
    </div>
  @endif
</div>
