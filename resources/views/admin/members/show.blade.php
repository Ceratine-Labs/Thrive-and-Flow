@extends('layouts.admin')

@section('title', $member->name)

@section('content')
<div class="adm-top">
  <div>
    <p class="crumb" style="margin-bottom:6px"><a href="{{ route('admin.members.index') }}">Members</a> / <span class="here">{{ $member->name }}</span></p>
    <h1>{{ $member->name }}</h1>
  </div>
  @if ($member->stageLabel())<span class="chip">{{ $member->stageLabel() }}</span>@endif
  @if ($current)
    <span class="tstat {{ $current->status->tint() }}">{{ $current->status->label() }}</span>
  @else
    <span class="tstat dim">No live membership</span>
  @endif
</div>

<div class="adm-grid">
  <div style="display:flex;flex-direction:column;gap:15px;min-width:0">
    <div class="chart-card">
      <h3>At a glance</h3>
      <div class="tblwrap"><table class="adm-table">
        <tbody>
          <tr><td class="muted">Email</td><td>{{ $member->email }}</td></tr>
          <tr><td class="muted">Joined</td><td class="num">{{ $member->created_at->format('j M Y') }}</td></tr>
          <tr><td class="muted">Classes completed</td><td class="num">{{ $completed }}</td></tr>
          <tr><td class="muted">Last active</td><td class="num">{{ $lastActive?->diffForHumans() ?? '—' }}</td></tr>
          @if ($member->due_date)<tr><td class="muted">Due date</td><td class="num">{{ $member->due_date->format('j M Y') }}</td></tr>@endif
          @if ($member->baby_born_at)<tr><td class="muted">Baby born</td><td class="num">{{ $member->baby_born_at->format('j M Y') }}</td></tr>@endif
        </tbody>
      </table></div>
    </div>

    <div class="chart-card">
      <h3>Membership history</h3>
      <div class="tblwrap"><table class="adm-table">
        <thead><tr><th>Plan</th><th>Status</th><th>Trial ends</th><th>Paid until</th><th>Note</th></tr></thead>
        <tbody>
        @forelse ($member->subscriptions->sortByDesc('id') as $sub)
          <tr>
            <td>{{ $sub->plan->name }}</td>
            <td><span class="tstat {{ $sub->status->tint() }}">{{ $sub->status->label() }}</span></td>
            <td class="num">{{ $sub->trial_ends_at?->format('j M') ?? '—' }}</td>
            <td class="num">{{ $sub->current_period_ends_at?->format('j M Y') ?? '—' }}</td>
            <td class="muted">{{ $sub->notes ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted">Never had a plan.</td></tr>
        @endforelse
        </tbody>
      </table></div>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:15px;min-width:0">
    <div class="chart-card">
      <h3>Record a payment</h3>
      <p class="csub" style="margin-bottom:12px">Activates or extends the plan by one period — use this for EFT until PayFast is wired in.</p>
      <form method="POST" action="{{ route('admin.members.grant', $member) }}">
        @csrf
        <div class="field">
          <label>Plan</label>
          <select class="input" name="plan_key">
            @foreach ($plans as $plan)
              <option value="{{ $plan->key }}">{{ $plan->name }} — {{ $plan->priceLine() }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Note <span style="font-weight:400;color:var(--faint)">e.g. EFT ref</span></label>
          <input class="input" name="note" placeholder="EFT ref 4471">
        </div>
        <button class="btn dark sm">Activate / extend</button>
      </form>
    </div>

    <div class="chart-card">
      <h3>Actions</h3>
      <div style="display:flex;flex-direction:column;gap:9px;margin-top:6px">
        @if ($current)
          <form method="POST" action="{{ route('admin.members.end', $member) }}"
                onsubmit="return confirm('End {{ $member->firstName() }}’s membership now?');">
            @csrf
            <button class="btn danger xs">End membership</button>
          </form>
        @endif
        <form method="POST" action="{{ route('admin.members.role', $member) }}"
              onsubmit="return confirm('Change {{ $member->firstName() }}’s role?');">
          @csrf
          <button class="btn ghost xs">{{ $member->isAdmin() ? 'Make member' : 'Make studio admin' }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
