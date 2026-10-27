@extends('layouts.site')

@section('title', 'Log in')

@section('content')
<div class="auth-wrap">
  <div class="card auth-card">
    <h1>Welcome back</h1>
    <p class="sub">Your mat is where you left it.</p>

    @if ($errors->any())
      <div class="errbox"><ul>
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul></div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
      @csrf
      <div class="field">
        <label for="email">Email</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <label class="check" style="margin-bottom:18px"><input type="checkbox" name="remember" value="1">Keep me logged in</label>
      <button class="btn primary" style="width:100%">Log in</button>
    </form>
    <p class="auth-alt">New here? <a href="{{ route('register') }}">Start your free week</a></p>
  </div>
</div>
@endsection
