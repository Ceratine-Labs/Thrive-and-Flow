@extends('layouts.admin')

@section('title', 'Members')

@section('content')
<div class="adm-top">
  <h1>Members</h1>
  <form class="search" method="GET" action="{{ route('admin.members.index') }}">
    <svg><use href="#i-search"/></svg>
    <input name="q" value="{{ $q }}" placeholder="Name or email…" aria-label="Search members">
  </form>
</div>

<div class="chart-card">
  <div class="tblwrap"><table class="adm-table">
    <thead><tr><th>Member</th><th>Email</th><th>Stage</th><th>Status</th><th>Joined</th><th></th></tr></thead>
    <tbody>
    @forelse ($members as $member)
      @php $sub = $member->currentSubscription(); @endphp
      <tr>
        <td><a href="{{ route('admin.members.show', $member) }}" style="display:flex;align-items:center;gap:9px">
          <span class="avn {{ ['','blush','sand'][$member->id % 3] }}">{{ $member->initials() }}</span>{{ $member->name }}</a></td>
        <td class="muted">{{ $member->email }}</td>
        <td>{{ $member->stageLabel() ?? '—' }}</td>
        <td>@if ($sub)<span class="tstat {{ $sub->status->tint() }}">{{ $sub->status->label() }}</span>
            @else<span class="tstat dim">No plan</span>@endif</td>
        <td class="num">{{ $member->created_at->format('j M Y') }}</td>
        <td><a class="btn ghost xs" href="{{ route('admin.members.show', $member) }}">Open</a></td>
      </tr>
    @empty
      <tr><td colspan="6" class="muted">No members match{{ $q ? ' "'.$q.'"' : '' }}.</td></tr>
    @endforelse
    </tbody>
  </table></div>
  {{ $members->links() }}
</div>
@endsection
