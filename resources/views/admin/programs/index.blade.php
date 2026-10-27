@extends('layouts.admin')

@section('title', 'Programs')

@section('content')
<div class="adm-top">
  <h1>Programs &amp; classes</h1>
  <a class="btn dark sm right" href="{{ route('admin.programs.create') }}">
    <svg width="12" height="12"><use href="#i-plus"/></svg> New program</a>
</div>

@foreach ($programs as $program)
  <div class="b-row">
    <span class="bic {{ $program->color }}"><svg><use href="#i-{{ $program->icon }}"/></svg></span>
    <div>
      <b>{{ $program->title }}</b>
      <span class="bs">{{ $program->stage->label() }} · {{ $program->published_lessons_count }}/{{ $program->lessons_count }} classes live
        @if ($program->drip_by_week) · drips week by week @endif</span>
    </div>
    <span class="bmeta">
      <span class="tstat {{ $program->is_published ? 'good' : 'dim' }}">{{ $program->is_published ? 'Published' : 'Hidden' }}</span>
      <form method="POST" action="{{ route('admin.programs.publish', $program) }}">
        @csrf
        <button class="sw {{ $program->is_published ? 'on' : '' }}" role="switch"
                aria-checked="{{ $program->is_published ? 'true' : 'false' }}" aria-label="Toggle published"></button>
      </form>
      <a class="btn ghost xs" href="{{ route('programs.show', $program) }}">View</a>
      <a class="btn dark xs" href="{{ route('admin.programs.builder', $program) }}">Open builder</a>
    </span>
  </div>
@endforeach
@endsection
