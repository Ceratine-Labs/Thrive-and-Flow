@extends('layouts.base')

@section('body')
  <nav class="app-nav"><div class="wrap app-nav-in">
    <a class="logo" href="{{ route('studio.dashboard') }}">
      <svg class="lotus" style="width:26px;height:26px"><use href="#i-lotus"/></svg>
      <span class="wm">Thrive <i>&amp;</i> Flow</span>
    </a>
    <span class="app-links">
      <a href="{{ route('studio.dashboard') }}" class="{{ request()->routeIs('studio.dashboard') ? 'on' : '' }}">Today</a>
      <a href="{{ route('programs.index') }}">Browse classes</a>
      <a href="{{ route('pricing') }}">Membership</a>
    </span>
    <span class="app-right">
      @hasSection('streak')
        @yield('streak')
      @endif
      <span class="avn blush" title="{{ auth()->user()->name }}">{{ auth()->user()->initials() }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn ghost xs">Log out</button>
      </form>
    </span>
  </div></nav>
  @yield('content')
@endsection
