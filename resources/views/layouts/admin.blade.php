@extends('layouts.base')

@section('body')
<div class="adm">
  <aside class="adm-side">
    <a class="logo" href="{{ route('admin.dashboard') }}">
      <svg class="lotus" style="width:26px;height:26px"><use href="#i-lotus"/></svg>
      <span><span class="wm">Thrive <i>&amp;</i> Flow</span><br><span class="sub">Studio Manager</span></span>
    </a>
    <a class="anav {{ request()->routeIs('admin.dashboard') ? 'on' : '' }}" href="{{ route('admin.dashboard') }}">
      <svg><use href="#i-home"/></svg>Overview</a>
    <a class="anav {{ request()->routeIs('admin.programs.*') ? 'on' : '' }}" href="{{ route('admin.programs.index') }}">
      <svg><use href="#i-grid"/></svg>Programs <span class="cnt num">{{ \App\Models\Program::count() }}</span></a>
    <a class="anav {{ request()->routeIs('admin.members.*') ? 'on' : '' }}" href="{{ route('admin.members.index') }}">
      <svg><use href="#i-users"/></svg>Members <span class="cnt num">{{ \App\Models\User::where('role', 'member')->count() }}</span></a>
    <a class="anav {{ request()->routeIs('admin.plans.*') ? 'on' : '' }}" href="{{ route('admin.plans.index') }}">
      <svg><use href="#i-card"/></svg>Plans &amp; pricing</a>
    <a class="anav" href="{{ route('home') }}"><svg><use href="#i-eye"/></svg>View site</a>
    <span class="who">
      <span class="avn" style="width:32px;height:32px;font-size:10.5px">{{ auth()->user()->initials() }}</span>
      <span><b>{{ auth()->user()->name }}</b><span>{{ auth()->user()->email }}</span></span>
    </span>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout">Log out</button>
    </form>
  </aside>
  <div class="adm-main">
    @if ($errors->any())
      <div class="errbox"><ul>
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul></div>
    @endif
    @yield('content')
  </div>
</div>
@endsection
