@extends('layouts.admin')

@section('title', 'Resolution Details')

@section('content')
<div class="page-header">
    <h1>Resolution Details</h1>
    <a href="{{ route('admin.resolutions.index') }}" class="btn">&laquo; Back to List</a>
</div>

<div class="card">
    <table class="details">
        <tr><th>Resolution Number</th><td>{{ $resolution->resolution_number }}</td></tr>
        <tr><th>Title</th><td>{{ $resolution->title }}</td></tr>
        <tr><th>Subject</th><td>{{ $resolution->subject }}</td></tr>
        <tr><th>Date Passed</th><td>{{ $resolution->date_passed->format('F d, Y') }}</td></tr>
        <tr><th>Author/Sponsor</th><td>{{ $resolution->author_sponsor }}</td></tr>
        <tr><th>Summary / Remarks</th><td>{!! nl2br(e($resolution->summary ?? '-')) !!}</td></tr>
        <tr><th>Record Created</th><td>{{ $resolution->created_at->format('M d, Y h:i A') }}</td></tr>
        <tr><th>Last Updated</th><td>{{ $resolution->updated_at->format('M d, Y h:i A') }}</td></tr>
    </table>

    <div class="form-actions">
        <a href="{{ route('admin.resolutions.edit', $resolution) }}" class="btn btn-warning">Edit</a>
        <form method="POST" action="{{ route('admin.resolutions.destroy', $resolution) }}" class="inline"
              onsubmit="return confirm('Are you sure you want to delete this resolution? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
@endsection
