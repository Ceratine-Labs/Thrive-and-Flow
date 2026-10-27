@extends('layouts.site')
@section('title', 'Movement for every chapter of motherhood')
@section('content')
<section class="tf-hero wrap" aria-labelledby="hero-title">
  <div class="tf-hero-copy">
    @if (is_file(public_path('images/roche-portrait.webp')))
    <img class="tf-hero-portrait" src="{{ asset('images/roche-portrait.webp') }}" alt="Roche smiling in the studio" width="900" height="1350">
    @endif
    <p class="eyebrow">Your body. Your pace. Your chapter.</p>
    <h1 id="hero-title">A little movement.<br>A little more <em>you.</em></h1>
    <p class="tf-intro">From the first flutter to the fourth trimester. Make space for strength, a softer stretch, and a moment that’s just yours.</p>
    <div class="tf-hero-actions">
      <a class="btn dark" href="{{ route('register') }}">Find your flow <svg width="16" height="16" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      <a class="tf-text-link" href="#movement"><span class="tf-play-icon" aria-hidden="true">▷</span> Explore a movement</a>
    </div>
    <p class="tf-small-note">{{ $monthly?->trial_days ?? 7 }} days to explore · From {{ $monthly?->priceRand() ?? 'R249' }}/month</p>
    <div class="tf-hero-signoff"><span class="tf-mini-flower" aria-hidden="true">✳</span> A little care goes a long way.</div>
  </div>
  <div class="tf-scene" data-scene data-active-scene="0" data-paused="true">
    <div class="tf-scene-topline"><span><i></i> A moment in your studio</span><button type="button" class="tf-scene-pause" data-scene-pause aria-pressed="true">Play scene <span aria-hidden="true">▷</span></button></div>
    <div class="tf-scene-art" id="studio-scene" role="img" aria-label="An illustrated, light-filled prenatal studio. A mother sits comfortably on her mat.">
      <img class="tf-room-image" src="{{ asset('images/prenatal-scene.webp') }}" alt="" width="1536" height="1024" fetchpriority="high">
      <div class="tf-scene-alternate tf-scene-space" aria-hidden="true"><span class="tf-scene-window"></span><img src="{{ asset('images/movement-side-reach.webp') }}" alt="" width="1024" height="1024"></div>
      <div class="tf-scene-alternate tf-scene-own" aria-hidden="true"><span class="tf-scene-window"></span><img src="{{ asset('images/movement-calf.webp') }}" alt="" width="1024" height="1024"></div>
      <div class="tf-light" aria-hidden="true"></div>
      <div class="tf-breath-rings" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="tf-scene-note" data-scene-note><span class="tf-note-icon" aria-hidden="true">✳</span><span>Let your shoulders soften.<small>There’s no rush here.</small></span></div>
      <span class="tf-scene-chapter" aria-hidden="true">01 / SETTLE IN</span>
    </div>
    <div class="tf-scene-caption"><div><span class="tf-scene-kicker">A LITTLE SPACE FOR YOU</span><h2 data-scene-title>Arrive just as you are.</h2><p data-scene-copy>Roll out your mat. Find a comfortable seat. Take a moment to settle.</p></div><a href="#movement" class="tf-round-link" aria-label="Explore movement demos">↗</a></div>
    <div class="tf-scene-tabs" role="group" aria-label="Choose a studio scene">
      <button type="button" data-scene-select="0" aria-pressed="true"><span>01</span> Settle in</button>
      <button type="button" data-scene-select="1" aria-pressed="false"><span>02</span> Find space</button>
      <button type="button" data-scene-select="2" aria-pressed="false"><span>03</span> Make it yours</button>
    </div>
  </div>
</section>
<div class="tf-values"><div class="wrap"><span><svg aria-hidden="true"><use href="#i-heart"/></svg> From bump to beyond</span><span><svg aria-hidden="true"><use href="#i-play"/></svg> {{ $lessonCount }} guided classes</span><span><svg aria-hidden="true"><use href="#i-clock"/></svg> At home. In your own time.</span><span><svg aria-hidden="true"><use href="#i-lotus"/></svg> Room for every kind of day</span></div></div>
<section class="wrap tf-chapters" aria-labelledby="chapters-title">
  <div class="tf-section-head"><div><p class="eyebrow">One studio. So many beginnings.</p><h2 id="chapters-title">Meet yourself <em>where you are.</em></h2></div><p>Your needs change. Your movement can too. Find a starting point for the chapter you’re in.</p></div>
  <div class="tf-chapter-grid">
    <a class="tf-chapter" href="{{ route('programs.index', ['stage' => 'pregnancy']) }}"><span class="tf-chapter-number">01 / PREGNANCY</span><h3>Growing, together.</h3><p>Trimester-led strength, mobility and breathing, with space to adapt.</p><span class="tf-chapter-link">Explore prenatal <span aria-hidden="true">↗</span></span></a>
    <a class="tf-chapter" href="{{ route('programs.index', ['stage' => 'postpartum']) }}"><span class="tf-chapter-number">02 / POSTNATAL</span><h3>A softer return.</h3><p>Reconnect with your breath and rebuild gradually, when you’re ready.</p><span class="tf-chapter-link">Explore postnatal <span aria-hidden="true">↗</span></span></a>
    <a class="tf-chapter" href="{{ route('programs.index', ['stage' => 'any']) }}"><span class="tf-chapter-number">03 / EVERYDAY YOU</span><h3>Little moments count.</h3><p>A stretch between feeds. A moment on the mat. A little room for yourself.</p><span class="tf-chapter-link">Explore the library <span aria-hidden="true">↗</span></span></a>
  </div>
</section>
@include('partials.movement-studio')
<section class="wrap tf-programs" aria-labelledby="programs-title">
  <div class="tf-section-head"><div><p class="eyebrow">Your next chapter starts here</p><h2 id="programs-title">Small steps. <em>Real connection.</em></h2></div><a class="tf-text-link" href="{{ route('programs.index') }}">Explore all programs <span aria-hidden="true">↗</span></a></div>
  <div class="grid3">@foreach ($featured as $program) @include('partials.program-card', ['program' => $program]) @endforeach</div>
</section>
<section class="tf-approach" id="how" aria-labelledby="approach-title"><div class="wrap tf-approach-grid">
  <div><p class="eyebrow">Less pressure. More presence.</p><h2 id="approach-title">You don’t need to<br><em>do it all.</em></h2><p class="tf-intro">You just need a place to begin. We bring the structure; you bring the version of yourself that showed up today.</p><a class="tf-text-link" href="{{ route('programs.index') }}">Get to know the studio <span aria-hidden="true">↗</span></a><span class="tf-approach-flower" aria-hidden="true">✳</span></div>
  <ol class="tf-steps">
    <li><span>01</span><div><h3>Find your chapter.</h3><p>Tell the studio where you are in pregnancy or after birth. Explore the classes organised for your week.</p></div></li>
    <li><span>02</span><div><h3>Get to know the movement.</h3><p>Read the course guide, explore the illustrations, and check modifications before you press play.</p></div></li>
    <li><span>03</span><div><h3>Make room for your day.</h3><p>Choose a class, take breaks, and come back when it works for you. Your saved classes and progress stay in your studio.</p></div></li>
  </ol>
</div></section>
<section class="wrap tf-care" id="nutrition" aria-labelledby="care-title"><div class="tf-care-header"><p class="eyebrow">Care beyond the mat</p><h2 id="care-title">Nourish the rest of <em>your day.</em></h2></div><p>Curious about nutrition coaching alongside your movement? Talk to the team about the support available, your goals, and what fits your routine.</p><a class="btn ghost" href="mailto:hello@thriveandflow.co.za?subject=Nutrition%20coaching%20enquiry">Let’s talk nutrition <span aria-hidden="true">↗</span></a></section>
<section class="wrap tf-faq" aria-labelledby="faq-title"><div><p class="eyebrow">A little reassurance</p><h2 id="faq-title">Before you <em>begin.</em></h2><p>Start with curiosity. Keep your own needs at the centre.</p></div><div class="tf-questions">
  <details><summary>Do I need to be fit to get started?<span aria-hidden="true">+</span></summary><p>You don’t need to arrive at a particular fitness level. Speak with your midwife or doctor about what’s suitable for you, especially if you’re new to exercise, have symptoms, or have been advised to limit activity. Start gradually and adapt the session to your comfort.</p></details>
  <details><summary>What will I need at home?<span aria-hidden="true">+</span></summary><p>A little clear space, a comfortable mat and water are a useful start. Some classes use a resistance band, light weights or a stable chair. Each course guide explains what to have nearby.</p></details>
  <details><summary>When can I begin after having my baby?<span aria-hidden="true">+</span></summary><p>Recovery is individual. The type of birth, any complications and your symptoms all matter. Your maternity team or pelvic health physiotherapist can help you decide when and how to return. A week number in the studio is a way to organise content, not medical clearance.</p></details>
  <details><summary>Can I explore before I join?<span aria-hidden="true">+</span></summary><p>Yes. Try the illustrated movement explorer and look for free-preview classes in the programs. A studio membership starts with a {{ $monthly?->trial_days ?? 7 }}-day trial. Visit <a href="{{ route('pricing') }}">membership</a> for the current plans and details.</p></details>
</div></section>
<section class="tf-invitation"><div class="wrap"><span class="tf-invitation-flower" aria-hidden="true">✳</span><p class="eyebrow center">There’s a place for you here</p><h2>Your next chapter.<br><em>A little more supported.</em></h2><p>Explore the studio for {{ $monthly?->trial_days ?? 7 }} days. Find your rhythm, one class at a time.</p><a class="btn dark" href="{{ route('register') }}">Start your free week <span aria-hidden="true">↗</span></a><span class="tf-small-note">{{ $monthly?->priceRand() ?? 'R249' }}/month after your trial · See <a href="{{ route('pricing') }}">membership details</a></span></div></section>
@endsection
