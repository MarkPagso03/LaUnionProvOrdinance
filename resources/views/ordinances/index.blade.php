@extends('layouts.admin')

@section('title', 'Ordinances')

@section('content')
<div class="page-header">
    <h1>Provincial Ordinances</h1>
    <a href="{{ route('admin.ordinances.create') }}" class="btn btn-primary">+ Add Ordinance</a>
</div>

<form method="GET" action="{{ route('admin.ordinances.index') }}" class="card filter-form">
    <div class="field">
        <label for="number">Ordinance Number</label>
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
        <a href="{{ route('admin.ordinances.index') }}" class="btn">Reset</a>
    </div>
</form>

<p class="muted">{{ $ordinances->total() }} record(s) found.</p>

<table class="table">
    <thead>
        <tr>
            <th>Ordinance No.</th>
            <th>Title</th>
            <th>Subject</th>
            <th>Date Passed</th>
            <th>Author/Sponsor</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($ordinances as $ordinance)
            <tr>
                <td>{{ $ordinance->ordinance_number }}</td>
                <td>{{ $ordinance->title }}</td>
                <td>{{ $ordinance->subject }}</td>
                <td>{{ $ordinance->date_passed->format('M d, Y') }}</td>
                <td>{{ $ordinance->author_sponsor }}</td>
                <td class="actions">
                    <a href="{{ route('admin.ordinances.show', $ordinance) }}" class="btn btn-small">View</a>
                    <a href="{{ route('admin.ordinances.edit', $ordinance) }}" class="btn btn-small btn-warning">Edit</a>
                    <form method="POST" action="{{ route('admin.ordinances.destroy', $ordinance) }}" class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this ordinance? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center muted">No ordinances found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $ordinances->links('partials.pagination') }}
@endsection
