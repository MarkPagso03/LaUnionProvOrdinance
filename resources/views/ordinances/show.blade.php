@extends('layouts.admin')

@section('title', 'Ordinance Details')

@section('content')
<div class="page-header">
    <h1>Ordinance Details</h1>
    <a href="{{ route('admin.ordinances.index') }}" class="btn">&laquo; Back to List</a>
</div>

<div class="card">
    <table class="details">
        <tr><th>Ordinance Number</th><td>{{ $ordinance->ordinance_number }}</td></tr>
        <tr><th>Title</th><td>{{ $ordinance->title }}</td></tr>
        <tr><th>Subject</th><td>{{ $ordinance->subject }}</td></tr>
        <tr><th>Date Passed</th><td>{{ $ordinance->date_passed->format('F d, Y') }}</td></tr>
        <tr><th>Author/Sponsor</th><td>{{ $ordinance->author_sponsor }}</td></tr>
        <tr><th>Summary / Remarks</th><td>{!! nl2br(e($ordinance->summary ?? '-')) !!}</td></tr>
        <tr><th>Record Created</th><td>{{ $ordinance->created_at->format('M d, Y h:i A') }}</td></tr>
        <tr><th>Last Updated</th><td>{{ $ordinance->updated_at->format('M d, Y h:i A') }}</td></tr>
    </table>

    <div class="form-actions">
        <a href="{{ route('admin.ordinances.edit', $ordinance) }}" class="btn btn-warning">Edit</a>
        <form method="POST" action="{{ route('admin.ordinances.destroy', $ordinance) }}" class="inline"
              onsubmit="return confirm('Are you sure you want to delete this ordinance? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
@endsection
