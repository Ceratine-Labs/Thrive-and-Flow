@extends('layouts.site')

@section('title', 'Start your free week')

@section('content')
<div class="auth-wrap" style="max-width:520px">
  <div class="card auth-card">
    <h1>Let's get you moving</h1>
    <p class="sub">Create your account — your first week of the studio is free.</p>

    @if ($errors->any())
      <div class="errbox"><ul>
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul></div>
    @endif

    <form method="POST" action="{{ route('register.attempt') }}">
      @csrf
      @if ($plan)<input type="hidden" name="plan" value="{{ $plan }}">@endif

      <div class="field">
        <label for="name">Your name</label>
        <input class="input" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
      </div>
      <div class="field">
        <label for="email">Email</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
      </div>

      <div class="field">
        <label>Where are you right now?</label>
        <div class="radio-cards">
          <label class="radio-card"><input type="radio" name="stage" value="pregnant" {{ old('stage') === 'pregnant' ? 'checked' : '' }} required>Pregnant</label>
          <label class="radio-card"><input type="radio" name="stage" value="postpartum" {{ old('stage') === 'postpartum' ? 'checked' : '' }}>New mom</label>
          <label class="radio-card"><input type="radio" name="stage" value="browsing" {{ old('stage') === 'browsing' ? 'checked' : '' }}>Just browsing</label>
        </div>
      </div>

      <div class="field" id="f-due" hidden>
        <label for="due_date">Your due date <span style="font-weight:400;color:var(--faint)">— so the studio opens on your week</span></label>
        <input class="input" type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
      </div>
      <div class="field" id="f-born" hidden>
        <label for="baby_born_at">Baby's birth date</label>
        <input class="input" type="date" id="baby_born_at" name="baby_born_at" value="{{ old('baby_born_at') }}">
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input class="input" type="password" id="password" name="password" required autocomplete="new-password">
      </div>
      <div class="field">
        <label for="password_confirmation">Password, once more</label>
        <input class="input" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
      </div>

      <button class="btn primary" style="width:100%;margin-top:6px">Create my account</button>
      <p class="tiny muted" style="text-align:center;margin-top:12px">No card needed. Always check with your healthcare provider before starting a new exercise program.</p>
    </form>
    <p class="auth-alt">Already a member? <a href="{{ route('login') }}">Log in</a></p>
  </div>
</div>

<script>
  (function () {
    var due = document.getElementById('f-due');
    var born = document.getElementById('f-born');
    function sync() {
      var v = (document.querySelector('input[name="stage"]:checked') || {}).value;
      due.hidden = v !== 'pregnant';
      born.hidden = v !== 'postpartum';
    }
    document.querySelectorAll('input[name="stage"]').forEach(function (r) { r.addEventListener('change', sync); });
    sync();
  })();
</script>
@endsection
