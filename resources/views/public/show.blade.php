@extends('layouts.public')

@section('title', $label . ' Details')

@section('content')
<div class="page-header">
    <h1>{{ $label }} Details</h1>
    <a href="{{ route('home') }}" class="btn">&laquo; Back to Search</a>
</div>

<div class="card">
    <span class="badge badge-{{ strtolower($label) }}">{{ strtoupper($label) }}</span>

    <table class="details">
        <tr><th>{{ $label }} Number</th><td>{{ $record->number }}</td></tr>
        <tr><th>Title</th><td>{{ $record->title }}</td></tr>
        <tr><th>Subject</th><td>{{ $record->subject }}</td></tr>
        <tr><th>Date Passed</th><td>{{ $record->date_passed->format('F d, Y') }}</td></tr>
        <tr><th>Author/Sponsor</th><td>{{ $record->author_sponsor }}</td></tr>
        <tr><th>Summary / Remarks</th><td>{!! nl2br(e($record->summary ?? '-')) !!}</td></tr>
    </table>
</div>
@endsection
