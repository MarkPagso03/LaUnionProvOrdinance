@extends('layouts.public')

@section('title', 'Administrator Login')

@section('content')
<div class="login-box card">
    <h1>Administrator Login</h1>
    <p class="muted">Authorized users only.</p>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="field">
            <label class="checkbox"><input type="checkbox" name="remember" value="1"> Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary">Log In</button>
        <a href="{{ route('home') }}" class="btn">Back to Public Search</a>
    </form>
</div>
@endsection
