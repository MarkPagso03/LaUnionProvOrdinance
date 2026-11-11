<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Search') | Provincial Legislative Records</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="navbar navbar-public">
    <div class="container nav-inner">
        <span class="brand">PROVINCIAL LEGISLATIVE RECORDS - PUBLIC SEARCH</span>

        <nav class="nav-links">
            <a href="{{ route('home') }}">Search</a>
            @auth
                <a href="{{ route('admin.dashboard') }}">Go to Admin Area</a>
            @else
                <a href="{{ route('login') }}">Admin Login</a>
            @endauth
        </nav>
    </div>
</header>

<main class="container">
    @include('partials.messages')
    @yield('content')
</main>

<footer class="footer">Provincial Legislative Records - Public view-only access</footer>
</body>
</html>
