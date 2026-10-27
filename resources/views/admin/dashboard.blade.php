@extends('layouts.admin')

@section('title', 'Studio Manager')

@section('content')
<div class="adm-top">
  <h1>Your studio, today</h1>
  <form class="search" method="GET" action="{{ route('admin.members.index') }}">
    <svg><use href="#i-search"/></svg>
    <input name="q" placeholder="Search members…" aria-label="Search members">
  </form>
  <a class="btn dark sm" href="{{ route('admin.programs.index') }}"><svg width="12" height="12"><use href="#i-plus"/></svg> Manage content</a>
</div>

<div class="kpis">
  <div class="kpi"><span class="kl">Active members</span>
    <span class="kv"><span class="num" data-count="{{ $activeMembers }}">{{ $activeMembers }}</span></span>
    <span class="ks">with a live trial or membership</span></div>
  <div class="kpi"><span class="kl">Monthly recurring revenue</span>
    <span class="kv"><span>R<span class="num" data-count="{{ str_replace(' ', '', $mrrRand) }}">{{ $mrrRand }}</span></span></span>
    <span class="ks">annuals counted at 1/12 per month</span></div>
  <div class="kpi"><span class="kl">Trials running</span>
    <span class="kv"><span class="num" data-count="{{ $trialsNow }}">{{ $trialsNow }}</span></span>
    <span class="ks">free weeks in progress right now</span></div>
  <div class="kpi"><span class="kl">Classes completed</span>
    <span class="kv"><span class="num" data-count="{{ $completionsThisWeek }}">{{ $completionsThisWeek }}</span></span>
    <span class="ks">by members in the last 7 days</span></div>
</div>

<div class="adm-grid">
  <div style="display:flex;flex-direction:column;gap:15px;min-width:0">
    <div class="chart-card">
      <h3>New members per week</h3>
      <p class="csub">Last 8 weeks · hover for detail</p>
      <div class="barwrap">
        <svg id="barsvg" viewBox="0 0 560 210" style="width:100%;height:auto" role="img"
             aria-label="Bar chart of new member signups per week over the last 8 weeks"></svg>
        <div class="ctip" id="ctip" hidden></div>
      </div>
      <script type="application/json" id="bars-data">@json($weeks)</script>
    </div>

    <div class="chart-card">
      <h3>Recent signups</h3>
      <div class="tblwrap"><table class="adm-table">
        <thead><tr><th>Member</th><th>Stage</th><th>Status</th><th>Joined</th></tr></thead>
        <tbody>
        @forelse ($recent as $member)
          @php $sub = $liveByUser[$member->id] ?? null; @endphp
          <tr>
            <td><a href="{{ route('admin.members.show', $member) }}" style="display:flex;align-items:center;gap:9px">
              <span class="avn {{ ['','blush','sand'][$member->id % 3] }}">{{ $member->initials() }}</span>{{ $member->name }}</a></td>
            <td>{{ $member->stageLabel() ?? '—' }}</td>
            <td>@if ($sub)<span class="tstat {{ $sub->status->tint() }}">{{ $sub->status->label() }}{{ $sub->isTrial() && $sub->trial_ends_at ? ' · ends '.$sub->trial_ends_at->format('j M') : '' }}</span>
                @else<span class="tstat dim">No plan</span>@endif</td>
            <td class="num">{{ $member->created_at->format('j M') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted">No members yet — share the free week!</td></tr>
        @endforelse
        </tbody>
      </table></div>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:15px;min-width:0">
    <div class="chart-card">
      <h3>Membership mix</h3>
      <p class="csub">{{ $mix['monthly'] + $mix['yearly'] }} live memberships</p>
      @php
        $total = max(1, $mix['monthly'] + $mix['yearly']);
        $C = 339.292;
        $monLen = round($mix['monthly'] / $total * $C, 1);
        $yrLen = round($mix['yearly'] / $total * $C, 1);
      @endphp
      <div style="display:flex;align-items:center;gap:18px;margin-top:10px;flex-wrap:wrap">
        <svg viewBox="0 0 140 140" width="122" height="122" role="img"
             aria-label="Donut chart: {{ $mix['monthly'] }} monthly and {{ $mix['yearly'] }} annual memberships">
          <circle cx="70" cy="70" r="54" fill="none" stroke="#F0E9E6" stroke-width="20"/>
          @if ($mix['monthly'] > 0)
            <circle cx="70" cy="70" r="54" fill="none" stroke="#C5604F" stroke-width="20"
                    stroke-dasharray="{{ max(0, $monLen - 2) }} {{ $C }}" transform="rotate(-90 70 70)"/>
          @endif
          @if ($mix['yearly'] > 0)
            <circle cx="70" cy="70" r="54" fill="none" stroke="#2B70A8" stroke-width="20"
                    stroke-dasharray="{{ max(0, $yrLen - 2) }} {{ $C }}" stroke-dashoffset="-{{ $monLen }}" transform="rotate(-90 70 70)"/>
          @endif
          <text x="70" y="66" text-anchor="middle" font-family="Cormorant Garamond,Georgia,serif" font-weight="600" font-size="26" fill="#2B333C">{{ $mix['monthly'] + $mix['yearly'] }}</text>
          <text x="70" y="84" text-anchor="middle" font-family="Poppins,system-ui,sans-serif" font-size="9" letter-spacing="1.5" fill="#66707A">LIVE</text>
        </svg>
        <div class="legend" style="flex-direction:column;gap:9px;margin:0">
          <span><i style="background:#C5604F"></i>Monthly · <b class="num">{{ $mix['monthly'] }}</b></span>
          <span><i style="background:#2B70A8"></i>Annual · <b class="num">{{ $mix['yearly'] }}</b></span>
        </div>
      </div>
    </div>

    <div class="chart-card">
      <h3>Most completed · 7 days</h3>
      @forelse ($topLessons as $row)
        <div class="mile"><span class="mic slate"><svg><use href="#i-play"/></svg></span>
          <span style="flex:1"><b>{{ \Illuminate\Support\Str::limit($row['lesson']->title, 34) }}</b>
            <span>{{ $row['lesson']->program->title }}</span></span>
          <span class="right num">{{ $row['plays'] }}</span></div>
      @empty
        <p class="tiny muted" style="margin-top:8px">No completions yet this week.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
