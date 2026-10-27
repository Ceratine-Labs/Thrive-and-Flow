@extends('layouts.site')

@section('title', 'Pricing')

@section('content')
<div class="wrap">
  <div style="text-align:center;padding:36px 0 28px;display:flex;flex-direction:column;align-items:center">
    <p class="eyebrow center">Pricing</p>
    <h1 style="font-size:clamp(30px,4.2vw,46px)">Simple, mom-maths <i style="color:var(--rose-ink)">friendly</i></h1>
    <p class="lede" style="color:var(--mut);margin:13px auto 24px;font-size:16px;max-width:52ch">One membership for the whole studio — less than a single in-person class per month.</p>
    @if ($monthly && $yearly)
      <div class="bill-toggle" role="group" aria-label="Billing period">
        <button class="on" data-bill="m">Monthly</button>
        <button data-bill="y">Annual <span class="save">2 months free</span></button>
      </div>
    @endif
  </div>

  <div class="grid3 plans">
    <div class="card plan">
      <h3>Free Week</h3>
      <p class="pdesc2">Seven days of everything. See if the studio fits your life before a cent leaves your account.</p>
      <div class="price"><b class="num">R0</b><span>for {{ $monthly?->trial_days ?? 7 }} days</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>Every program and class, unlocked</li>
        <li><svg><use href="#i-check"/></svg>No card needed to start</li>
        <li><svg><use href="#i-check"/></svg>Cancel from your studio in one tap</li>
      </ul>
      <form>
        <a class="btn ghost" style="width:100%" href="{{ auth()->check() ? route('pricing') : route('register') }}">
          {{ auth()->check() ? 'Included with membership' : 'Start free' }}</a>
      </form>
    </div>

    @if ($monthly)
      <div class="card plan feat">
        <span class="badge">Most loved</span>
        <h3>Studio Membership</h3>
        <p class="pdesc2">The full studio, every stage — from week 6 of pregnancy to mom &amp; baby classes.</p>
        <div class="price">
          <b class="num" id="mprice" data-monthly="{{ $monthly->priceRand() }}"
             data-yearly="{{ $yearly ? 'R'.number_format($yearly->price_cents / 1200, 0, '.', ' ') : $monthly->priceRand() }}">{{ $monthly->priceRand() }}</b>
          <span id="mper" data-monthly="per month"
                data-yearly="per month · billed {{ $yearly?->priceRand() }}/year">per month</span>
        </div>
        <ul>
          <li><svg><use href="#i-check"/></svg>Every guided session, streamed in HD</li>
          <li><svg><use href="#i-check"/></svg>Trimester &amp; postnatal programming that follows your week</li>
          <li><svg><use href="#i-check"/></svg>Mom &amp; baby classes from 6 weeks postpartum</li>
          <li><svg><use href="#i-check"/></svg>Progress, streaks and saved classes</li>
          <li><svg><use href="#i-check"/></svg>Cancel anytime — keep progress if you return</li>
        </ul>
        @auth
          <form method="POST" id="mform"
                action="{{ route('studio.subscribe', $monthly) }}"
                data-monthly="{{ route('studio.subscribe', $monthly) }}"
                data-yearly="{{ $yearly ? route('studio.subscribe', $yearly) : route('studio.subscribe', $monthly) }}">
            @csrf
            <button class="btn onink">Start your free week</button>
          </form>
        @else
          <form id="mform"><a class="btn onink" style="width:100%" href="{{ route('register', ['plan' => 'studio_monthly']) }}">Start your free week</a></form>
        @endauth
      </div>
    @endif

    @if ($guide)
      <div class="card plan">
        <h3>{{ $guide->name }}</h3>
        <p class="pdesc2">{{ $guide->description }}</p>
        <div class="price"><b class="num">{{ $guide->priceRand() }}</b><span>once-off</span></div>
        <ul>
          <li><svg><use href="#i-check"/></svg>13 structured technique lessons, yours for life</li>
          <li><svg><use href="#i-check"/></svg>Perfect alongside the membership — or on its own</li>
          <li><svg><use href="#i-check"/></svg>Companion workbook included</li>
        </ul>
        @auth
          <form method="POST" action="{{ route('studio.subscribe', $guide) }}">
            @csrf
            <button class="btn ghost">Buy the guide</button>
          </form>
        @else
          <form><a class="btn ghost" style="width:100%" href="{{ route('register', ['plan' => 'guide_once']) }}">Buy the guide</a></form>
        @endauth
      </div>
    @endif
  </div>

  <div class="pay-note">
    <span class="chip"><svg width="12" height="12"><use href="#i-lock"/></svg>&nbsp;Card &amp; EFT — PayFast checkout coming online</span>
    <span class="chip">Cancel in one tap</span>
    <span class="chip">POPIA-respectful data</span>
  </div>

  <div style="max-width:700px;margin:64px auto 76px" id="faq">
    <p class="eyebrow center">Questions, answered</p>
    <h2 style="text-align:center;font-size:clamp(23px,2.9vw,32px);margin-bottom:20px">Before you press play</h2>
    <div class="faq open">
      <button class="faq-q">Is it safe for me to start? <svg><use href="#i-plus"/></svg></button>
      <p class="faq-a">Every class is designed around pre- and postnatal safety — pelvic floor, core pressure and positioning included. That said, always check in with your midwife, GP or obstetrician before starting, and again if anything in your pregnancy changes.</p>
    </div>
    <div class="faq">
      <button class="faq-q">Which week do I start on? <svg><use href="#i-plus"/></svg></button>
      <p class="faq-a">Tell us your due date (or your baby's birth date) when you sign up and the studio opens on your exact week — Trimester 1 covers weeks 6–13, Trimester 2 weeks 14–27, Trimester 3 weeks 28–41. You can always move at your own pace.</p>
    </div>
    <div class="faq">
      <button class="faq-q">What equipment do I need? <svg><use href="#i-plus"/></svg></button>
      <p class="faq-a">A mat and a resistance band cover most classes. Some sessions add light dumbbells, a chair or bench, a yoga block (books work), and a pregnancy or pilates ball — a firm pillow stands in happily.</p>
    </div>
    <div class="faq">
      <button class="faq-q">Can I cancel whenever I want? <svg><use href="#i-plus"/></svg></button>
      <p class="faq-a">Yes — cancellation is one tap in your studio, no emails and no guilt. Your progress is saved, so if you come back after the newborn fog, everything is where you left it.</p>
    </div>
    <div class="faq">
      <button class="faq-q">What happens when my free week ends? <svg><use href="#i-plus"/></svg></button>
      <p class="faq-a">If the studio is right for you, your membership simply carries on at {{ $monthly?->priceRand() ?? 'R249' }}/month. If it's not the right season, cancel before the week is up and part as friends.</p>
    </div>
  </div>
</div>
@endsection
