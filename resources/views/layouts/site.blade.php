@extends('layouts.base')

@section('body-class', 'public-site')

@section('body')
  @include('partials.nav-public')
  <main id="main-content">@yield('content')</main>
  @include('partials.footer')
@endsection
