@extends('layouts.admin')

@section('title', 'Add Ordinance')

@section('content')
<h1>Add Ordinance</h1>

<form method="POST" action="{{ route('admin.ordinances.store') }}" class="card form">
    @csrf
    @include('ordinances._form', ['ordinance' => new \App\Models\Ordinance()])

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Ordinance</button>
        <a href="{{ route('admin.ordinances.index') }}" class="btn">Cancel</a>
    </div>
</form>
@endsection
