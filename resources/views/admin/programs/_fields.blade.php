@php /** Shared program fields — used by create + builder settings. */ @endphp
<div class="frow">
  <div class="field" style="flex:2 1 240px">
    <label>Title</label>
    <input class="input" name="title" value="{{ old('title', $program->title) }}" required>
  </div>
  <div class="field slim">
    <label>Stage</label>
    <select class="input" name="stage">
      @foreach (['pregnancy' => 'Pregnancy', 'postpartum' => 'Postpartum', 'mom_baby' => 'Mom & baby', 'any' => 'Any stage'] as $v => $label)
        <option value="{{ $v }}" @selected(old('stage', $program->stage?->value) === $v)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="field slim">
    <label>Type</label>
    <select class="input" name="kind">
      <option value="program" @selected(old('kind', $program->kind?->value) === 'program')>Weekly program</option>
      <option value="collection" @selected(old('kind', $program->kind?->value) === 'collection')>Class library</option>
    </select>
  </div>
</div>
<div class="field" style="margin-top:14px">
  <label>Subtitle <span style="font-weight:400;color:var(--faint)">— one warm sentence on the card</span></label>
  <input class="input" name="subtitle" value="{{ old('subtitle', $program->subtitle) }}">
</div>
<div class="field">
  <label>Description</label>
  <textarea class="input" name="description">{{ old('description', $program->description) }}</textarea>
</div>
<div class="frow">
  <div class="field slim"><label>From week</label>
    <input class="input num" type="number" name="weeks_from" min="1" max="42" value="{{ old('weeks_from', $program->weeks_from) }}"></div>
  <div class="field slim"><label>To week</label>
    <input class="input num" type="number" name="weeks_to" min="1" max="42" value="{{ old('weeks_to', $program->weeks_to) }}"></div>
  <div class="field slim"><label>Classes / week</label>
    <input class="input num" type="number" name="classes_per_week" min="1" max="7" value="{{ old('classes_per_week', $program->classes_per_week) }}"></div>
  <div class="field slim"><label>Level</label>
    <input class="input" name="level" value="{{ old('level', $program->level) }}" placeholder="All levels"></div>
  <div class="field slim"><label>Sort</label>
    <input class="input num" type="number" name="sort" min="0" max="999" value="{{ old('sort', $program->sort ?? 50) }}"></div>
</div>
<div class="frow" style="margin-top:14px">
  <div class="field slim">
    <label>Cover colour</label>
    <select class="input" name="color">
      @foreach (['slate' => 'Slate', 'blush' => 'Blush', 'sand' => 'Sand'] as $v => $label)
        <option value="{{ $v }}" @selected(old('color', $program->color ?? 'slate') === $v)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="field slim">
    <label>Cover glyph</label>
    <select class="input" name="icon">
      @foreach (['petal' => 'Petal', 'wave' => 'Wave', 'baby' => 'Mom & baby'] as $v => $label)
        <option value="{{ $v }}" @selected(old('icon', $program->icon ?? 'petal') === $v)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="field" style="flex:2 1 240px">
    <label>Equipment <span style="font-weight:400;color:var(--faint)">— comma separated</span></label>
    <input class="input" name="equipment_text" value="{{ old('equipment_text', implode(', ', $program->equipment ?? [])) }}">
  </div>
</div>
<label class="check" style="margin:6px 0 14px">
  <input type="checkbox" name="drip_by_week" value="1" @checked(old('drip_by_week', $program->drip_by_week))>
  Unlock weeks as each member reaches them (pregnancy programs)
</label>
