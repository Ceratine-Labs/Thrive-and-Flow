@extends('layouts.site')

@section('title', 'Programs & classes')

@section('content')
<div class="wrap cg-catalog">
  <header class="cg-catalog-intro">
    <div>
      <p class="eyebrow">The movement collection</p>
      <h1>A little stronger.<br>A little more <i>you.</i></h1>
      <p class="lede">A path through pregnancy, a gentle return after birth, or a few minutes just for you. Find the classes that meet you today.</p>
      <a class="cg-text-link" href="{{ route('home') }}#movement">Explore the movement guide <span aria-hidden="true">↗</span></a>
    </div>
    <aside class="cg-start-note">
      <span class="cg-note-mark" aria-hidden="true">01</span>
      <p class="eyebrow">Start where you are</p>
      <h2>Your pace is<br>the right pace.</h2>
      <p>Follow a weekly program or dip into the libraries. Every collection below is included in studio membership.</p>
      <a href="{{ route('pricing') }}">See membership <span aria-hidden="true">→</span></a>
    </aside>
  </header>

  <div class="card filterbar cg-filterbar" aria-label="Filter programs">
    <span class="fgroup"><span class="fl">Stage</span>
      <button type="button" class="fchip {{ $selectedStage === 'all' ? 'on' : '' }}" aria-pressed="{{ $selectedStage === 'all' ? 'true' : 'false' }}" data-f="stage" data-v="all">All</button>
      <button type="button" class="fchip {{ $selectedStage === 'pregnancy' ? 'on' : '' }}" aria-pressed="{{ $selectedStage === 'pregnancy' ? 'true' : 'false' }}" data-f="stage" data-v="pregnancy">Pregnancy</button>
      <button type="button" class="fchip {{ $selectedStage === 'postpartum' ? 'on' : '' }}" aria-pressed="{{ $selectedStage === 'postpartum' ? 'true' : 'false' }}" data-f="stage" data-v="postpartum">Postpartum</button>
      <button type="button" class="fchip {{ $selectedStage === 'mom_baby' ? 'on' : '' }}" aria-pressed="{{ $selectedStage === 'mom_baby' ? 'true' : 'false' }}" data-f="stage" data-v="mom_baby">Mom &amp; baby</button>
      <button type="button" class="fchip {{ $selectedStage === 'any' ? 'on' : '' }}" aria-pressed="{{ $selectedStage === 'any' ? 'true' : 'false' }}" data-f="stage" data-v="any">Any stage</button>
    </span>
    <span class="fgroup"><span class="fl">Type</span>
      <button type="button" class="fchip on" aria-pressed="true" data-f="focus" data-v="all">All</button>
      <button type="button" class="fchip" aria-pressed="false" data-f="focus" data-v="program">Programs</button>
      <button type="button" class="fchip" aria-pressed="false" data-f="focus" data-v="collection">Class libraries</button>
    </span>
    <span class="fcount" id="fcount" role="status" aria-live="polite">{{ $programs->filter(fn ($p) => $selectedStage === 'all' || $p->stage->value === $selectedStage || $p->stage->value === 'any')->count() }} collections</span>
  </div>

  <div class="grid3 cg-program-grid" id="pgrid" data-initial-stage="{{ $selectedStage }}">
    @forelse ($programs as $program)
      @include('partials.program-card', ['program' => $program])
    @empty
      <p class="muted">No programs published yet — check back soon.</p>
    @endforelse
  </div>
  <div class="cg-catalog-foot">
    <p><b>Small sessions count.</b> Rest, repeat a class, or try something shorter. Your course is here when you are.</p>
    <a class="cg-text-link" href="{{ route('home') }}#movement">See the illustrated movement guide <span aria-hidden="true">→</span></a>
  </div>
</div>
@endsection
