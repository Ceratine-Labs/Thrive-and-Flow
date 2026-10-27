@extends('layouts.admin')

@section('title', 'Plans & pricing')

@section('content')
<div class="adm-top"><h1>Plans &amp; pricing</h1></div>

<div class="grid3" style="align-items:start">
  @foreach ($plans as $plan)
    <div class="chart-card">
      <h3>{{ $plan->key }}</h3>
      <p class="csub">{{ $plan->subscriptions_count }} subscriptions all-time</p>
      <form method="POST" action="{{ route('admin.plans.update', $plan) }}" style="margin-top:14px">
        @csrf @method('PUT')
        <div class="field"><label>Name</label>
          <input class="input" name="name" value="{{ $plan->name }}" required></div>
        <div class="field"><label>Description</label>
          <input class="input" name="description" value="{{ $plan->description }}"></div>
        <div class="frow">
          <div class="field slim"><label>Price (R{{ $plan->interval->value === 'monthly' ? '/month' : ($plan->interval->value === 'yearly' ? '/year' : ' once') }})</label>
            <input class="input num" type="number" step="0.01" name="price_rand" value="{{ $plan->price_cents / 100 }}" required></div>
          <div class="field slim"><label>Trial days</label>
            <input class="input num" type="number" name="trial_days" min="0" max="60" value="{{ $plan->trial_days }}" required></div>
        </div>
        <label class="check" style="margin:10px 0 14px">
          <input type="checkbox" name="is_active" value="1" @checked($plan->is_active)>Shown on the pricing page
        </label>
        <button class="btn dark sm">Save plan</button>
      </form>
    </div>
  @endforeach
</div>
@endsection
