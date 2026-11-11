@extends('layouts.admin')

@section('title', 'Add Resolution')

@section('content')
<h1>Add Resolution</h1>

<form method="POST" action="{{ route('admin.resolutions.store') }}" class="card form">
    @csrf
    @include('resolutions._form', ['resolution' => new \App\Models\Resolution()])

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Resolution</button>
        <a href="{{ route('admin.resolutions.index') }}" class="btn">Cancel</a>
    </div>
</form>
@endsection
