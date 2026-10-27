@extends('layouts.admin')

@section('title', 'New program')

@section('content')
<div class="adm-top"><h1>New program</h1></div>

<div class="chart-card" style="max-width:720px">
  <form method="POST" action="{{ route('admin.programs.store') }}">
    @csrf
    @include('admin.programs._fields', ['program' => $program])
    <button class="btn dark" style="margin-top:8px">Create program</button>
  </form>
</div>
@endsection
