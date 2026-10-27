<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Pre & Postnatal Studio') · Thrive & Flow</title>
<meta name="description" content="Trimester-by-trimester workouts, postnatal rebuilding and mom & baby classes — streamed from home.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Poppins:wght@400;500;600&family=Spline+Sans+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="{{ asset('css/studio.css') }}?v=20261027">
<link rel="stylesheet" href="{{ asset('css/refresh.css') }}?v=20261027-intro">
<link rel="stylesheet" href="{{ asset('css/course-guides.css') }}?v=20261018">
<link rel="stylesheet" href="{{ asset('css/movement-studio.css') }}?v=20261027">
<link rel="icon" href="data:image/svg+xml,{{ rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="%235E6E7B" d="M16 4.5c-3.4 4.6-3.4 11.2 0 15.8 3.4-4.6 3.4-11.2 0-15.8z"/><path fill="%23E7C4C0" d="M8 10.5c-.8 5.8 2.2 10.4 8.2 11.8-.9-5.9-3.9-9.9-8.2-11.8zM24 10.5c.8 5.8-2.2 10.4-8.2 11.8.9-5.9 3.9-9.9 8.2-11.8z"/></svg>') }}">
</head>
<body class="@yield('body-class')">
@include('partials.icons')
@if (session('status'))
  <div class="flash" role="status">{{ session('status') }}</div>
@endif
@yield('body')
<script src="{{ asset('js/studio.js') }}" defer></script>
<script src="{{ asset('js/refresh.js') }}?v=20261018" defer></script>
<script src="{{ asset('js/movement-artwork.js') }}?v=20261026-focus" defer></script>
<script src="{{ asset('js/movement-studio.js') }}?v=20261026-focus" defer></script>
</body>
</html>
