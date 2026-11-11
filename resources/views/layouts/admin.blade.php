<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administrator') | Admin - Provincial Legislative Records</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="navbar navbar-admin">
    <div class="container nav-inner">
        <span class="brand">ADMINISTRATIVE AREA</span>

        <nav class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.ordinances.index') }}" class="{{ request()->routeIs('admin.ordinances.*') ? 'active' : '' }}">Ordinances</a>
            <a href="{{ route('admin.resolutions.index') }}" class="{{ request()->routeIs('admin.resolutions.*') ? 'active' : '' }}">Resolutions</a>
            <a href="{{ route('home') }}" target="_blank">Public Site</a>
        </nav>

        <div class="nav-user">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-small">Logout</button>
            </form>
        </div>
    </div>
</header>

<main class="container">
    @include('partials.messages')
    @yield('content')
</main>

<footer class="footer">Provincial Legislative Records - Administrative Area</footer>
</body>
</html>
