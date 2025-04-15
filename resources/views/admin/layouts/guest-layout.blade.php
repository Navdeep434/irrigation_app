<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('page-title', 'Smart Irrigation')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
</head>
<body>
  <div id="loader" class="loader-primary d-none">
    <svg width="120" height="30" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg">
      <circle class="circle circle-1" cx="20" cy="15" r="6"/>
      <circle class="circle circle-2" cx="40" cy="15" r="6"/>
      <circle class="circle circle-3" cx="60" cy="15" r="6"/>
      <circle class="circle circle-4" cx="80" cy="15" r="6"/>
      <circle class="circle circle-5" cx="100" cy="15" r="6"/>
    </svg>
  </div>

  <header class="auth-header">
    <svg class="leaf-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M12,2L4.5,20.29L5.21,21L18.29,7.92L19,8.62L12,22L12,2Z"/>
    </svg>
    <h1>Smart Irrigation</h1>
  </header>

  @yield('content')


</body>
</html>