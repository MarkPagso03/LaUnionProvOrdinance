@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1>Dashboard</h1>
<p class="muted">Welcome, {{ auth()->user()->name }}.</p>

<div class="stats">
    <div class="card stat">
        <div class="stat-number">{{ $ordinanceCount }}</div>
        <div>Ordinances</div>
        <a href="{{ route('admin.ordinances.index') }}" class="btn btn-small">Manage</a>
    </div>
    <div class="card stat">
        <div class="stat-number">{{ $resolutionCount }}</div>
        <div>Resolutions</div>
        <a href="{{ route('admin.resolutions.index') }}" class="btn btn-small">Manage</a>
    </div>
</div>

<div class="two-columns">
    <div class="card">
        <h2>Latest Ordinances</h2>
        <ul class="plain-list">
            @forelse ($latestOrdinances as $item)
                <li><a href="{{ route('admin.ordinances.show', $item) }}">{{ $item->ordinance_number }}</a> - {{ $item->title }}</li>
            @empty
                <li class="muted">No ordinances yet.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <h2>Latest Resolutions</h2>
        <ul class="plain-list">
            @forelse ($latestResolutions as $item)
                <li><a href="{{ route('admin.resolutions.show', $item) }}">{{ $item->resolution_number }}</a> - {{ $item->title }}</li>
            @empty
                <li class="muted">No resolutions yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
