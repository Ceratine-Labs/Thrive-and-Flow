<a class="skip-link" href="#main-content">Skip to content</a>
<header class="public-header"><div class="wrap">
<nav class="site-nav" aria-label="Main navigation">
  <a class="logo" href="{{ route('home') }}" aria-label="Thrive and Flow home">
    <svg class="lotus" aria-hidden="true"><use href="#i-lotus"/></svg>
    <span><span class="wm">Thrive <i>&amp;</i> Flow</span><br><span class="sub">Pre &amp; Postnatal Studio</span></span>
  </a>
  <div class="nav-links" id="public-navigation">
    <a href="{{ route('programs.index') }}" @class(['on' => request()->routeIs('programs.*')])>Our programs</a>
    <a href="{{ route('home') }}#movement">Explore movement</a>
    <a href="{{ route('home') }}#how">Our approach</a>
    <a href="{{ route('pricing') }}" @class(['on' => request()->routeIs('pricing')])>Membership</a>
  </div>
  <div class="nav-cta">
    @auth
      <a class="btn primary sm" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('studio.dashboard') }}">My studio <span aria-hidden="true">↗</span></a>
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-login">Log out</button></form>
    @else
      <a class="nav-login" href="{{ route('login') }}">Log in</a>
      <a class="btn primary sm" href="{{ route('register') }}">Start your free week <span aria-hidden="true">↗</span></a>
    @endauth
    <button class="nav-menu" type="button" aria-expanded="false" aria-controls="public-navigation" aria-label="Open navigation"><span></span><span></span></button>
  </div>
</nav></div></header>
