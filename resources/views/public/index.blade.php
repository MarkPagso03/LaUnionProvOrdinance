@extends('layouts.public')

@section('title', 'Search Legislative Records')

@section('content')
<h1>Search Provincial Ordinances and Resolutions</h1>
<p class="muted">Type a word from the title or subject. Partial words work too (for example, "agricultur" finds "agriculture").</p>

<form method="GET" action="{{ route('home') }}" class="card search-box">
    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Enter keyword..." aria-label="Keyword">

    <select name="type" aria-label="Record type">
        <option value="all" @selected($type === 'all')>Ordinances and Resolutions</option>
        <option value="ordinance" @selected($type === 'ordinance')>Ordinances only</option>
        <option value="resolution" @selected($type === 'resolution')>Resolutions only</option>
    </select>

    <button type="submit" class="btn btn-primary">Search</button>
    <a href="{{ route('home') }}" class="btn">Reset</a>
</form>

@if ($keyword !== '')
    <p>Results for: <strong>{{ $keyword }}</strong></p>
@else
    <p class="muted">Showing the latest records. Enter a keyword above to search.</p>
@endif

@if ($ordinances)
    @include('public._results', [
        'records'   => $ordinances,
        'label'     => 'Ordinance',
        'heading'   => 'Provincial Ordinances',
        'showRoute' => 'public.ordinances.show',
    ])
@endif

@if ($resolutions)
    @include('public._results', [
        'records'   => $resolutions,
        'label'     => 'Resolution',
        'heading'   => 'Provincial Resolutions',
        'showRoute' => 'public.resolutions.show',
    ])
@endif
@endsection
