@extends('layouts.admin')

@section('title', 'Edit Ordinance')

@section('content')
<h1>Edit Ordinance</h1>

<form method="POST" action="{{ route('admin.ordinances.update', $ordinance) }}" class="card form">
    @csrf
    @method('PUT')
    @include('ordinances._form')

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Ordinance</button>
        <a href="{{ route('admin.ordinances.show', $ordinance) }}" class="btn">Cancel</a>
    </div>
</form>
@endsection
