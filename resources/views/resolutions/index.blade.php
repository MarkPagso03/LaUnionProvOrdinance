@extends('layouts.admin')

@section('title', 'Resolutions')

@section('content')
<div class="page-header">
    <h1>Provincial Resolutions</h1>
    <a href="{{ route('admin.resolutions.create') }}" class="btn btn-primary">+ Add Resolution</a>
</div>

<form method="GET" action="{{ route('admin.resolutions.index') }}" class="card filter-form">
    <div class="field">
        <label for="number">Resolution Number</label>
        <input type="text" id="number" name="number" value="{{ request('number') }}">
    </div>
    <div class="field">
        <label for="title">Title (keyword)</label>
        <input type="text" id="title" name="title" value="{{ request('title') }}" placeholder="e.g. environment">
    </div>
    <div class="field">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" value="{{ request('subject') }}" placeholder="e.g. agriculture">
    </div>
    <div class="field">
        <label for="date_passed">Date Passed</label>
        <input type="date" id="date_passed" name="date_passed" value="{{ request('date_passed') }}">
    </div>
    <div class="field filter-buttons">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('admin.resolutions.index') }}" class="btn">Reset</a>
    </div>
</form>

<p class="muted">{{ $resolutions->total() }} record(s) found.</p>

<table class="table">
    <thead>
        <tr>
            <th>Resolution No.</th>
            <th>Title</th>
            <th>Subject</th>
            <th>Date Passed</th>
            <th>Author/Sponsor</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($resolutions as $resolution)
            <tr>
                <td>{{ $resolution->resolution_number }}</td>
                <td>{{ $resolution->title }}</td>
                <td>{{ $resolution->subject }}</td>
                <td>{{ $resolution->date_passed->format('M d, Y') }}</td>
                <td>{{ $resolution->author_sponsor }}</td>
                <td class="actions">
                    <a href="{{ route('admin.resolutions.show', $resolution) }}" class="btn btn-small">View</a>
                    <a href="{{ route('admin.resolutions.edit', $resolution) }}" class="btn btn-small btn-warning">Edit</a>
                    <form method="POST" action="{{ route('admin.resolutions.destroy', $resolution) }}" class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this resolution? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center muted">No resolutions found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $resolutions->links('partials.pagination') }}
@endsection
