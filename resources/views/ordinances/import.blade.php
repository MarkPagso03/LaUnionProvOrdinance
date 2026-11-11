@extends('layouts.admin')

@section('title', 'Import Ordinances')

@section('content')
<div class="page-header">
    <h1>Import Ordinances (CSV / Excel)</h1>
    <a href="{{ route('admin.ordinances.index') }}" class="btn">&laquo; Back to List</a>
</div>

@if (session('import_errors'))
    <div class="card">
        <h2>Nothing was imported</h2>
        <p>Please fix the rows below in your file and upload it again. Row numbers are the row numbers shown in Excel (row 1 is the header).</p>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">Row</th>
                    <th>Problem(s)</th>
                </tr>
            </thead>
            <tbody>
                @foreach (session('import_errors') as $item)
                    <tr>
                        <td>{{ $item['row'] }}</td>
                        <td>
                            <ul class="plain-list">
                                @foreach ($item['messages'] as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="card form">
    <form method="POST" action="{{ route('admin.ordinances.import.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label for="file">Choose file (.csv, .xlsx or .xls) <span class="required">*</span></label>
            <input type="file" id="file" name="file" accept=".csv,.xlsx,.xls" class="@error('file') is-invalid @enderror">
            @error('file')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Import</button>
            <a href="{{ route('admin.ordinances.import.template') }}" class="btn">Download CSV Template</a>
        </div>
    </form>
</div>

<div class="card">
    <h2>How to prepare the file</h2>
    <ul>
        <li>Row 1 must contain the column names. Each following row is one ordinance.</li>
        <li>Download the template above, open it in Excel, fill it in, and upload it (CSV or Excel format).</li>
        <li>All rows are checked first. If any row has a problem, <strong>nothing is imported</strong>.</li>
        <li>Dates must be real dates or written as <strong>YYYY-MM-DD</strong> (example: 2026-01-15).</li>
        <li>Maximum: 1,000 rows and 5 MB per file.</li>
    </ul>

    <table class="table">
        <thead>
            <tr><th>Column name</th><th>Required?</th><th>Rule</th></tr>
        </thead>
        <tbody>
            <tr><td>ordinance_number</td><td>Yes</td><td>Must be unique (not in the system and not repeated in the file). Up to 50 characters.</td></tr>
            <tr><td>title</td><td>Yes</td><td>Up to 500 characters.</td></tr>
            <tr><td>subject</td><td>Yes</td><td>Up to 255 characters.</td></tr>
            <tr><td>date_passed</td><td>Yes</td><td>A valid date, not in the future.</td></tr>
            <tr><td>author_sponsor</td><td>Yes</td><td>Up to 255 characters.</td></tr>
            <tr><td>summary</td><td>No</td><td>Optional remarks.</td></tr>
        </tbody>
    </table>
</div>
@endsection
