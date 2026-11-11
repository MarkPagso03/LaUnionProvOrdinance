@extends('layouts.admin')

@section('title', 'Edit Resolution')

@section('content')
<h1>Edit Resolution</h1>

<form method="POST" action="{{ route('admin.resolutions.update', $resolution) }}" class="card form">
    @csrf
    @method('PUT')
    @include('resolutions._form')

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Resolution</button>
        <a href="{{ route('admin.resolutions.show', $resolution) }}" class="btn">Cancel</a>
    </div>
</form>
@endsection
