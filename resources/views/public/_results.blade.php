<section class="results">
    <h2>{{ $heading }} <span class="muted">({{ $records->total() }} found)</span></h2>

    @forelse ($records as $record)
        <article class="result">
            <span class="badge badge-{{ strtolower($label) }}">{{ strtoupper($label) }}</span>
            <h3><a href="{{ route($showRoute, $record) }}">{{ $label }} No. {{ $record->number }}: {{ $record->title }}</a></h3>
            <p class="meta">
                Subject: {{ $record->subject }}
                &nbsp;|&nbsp; Date Passed: {{ $record->date_passed->format('F d, Y') }}
                &nbsp;|&nbsp; Author/Sponsor: {{ $record->author_sponsor }}
            </p>
        </article>
    @empty
        <p class="muted">No {{ strtolower($label) }} records found.</p>
    @endforelse

    {{ $records->links('partials.pagination') }}
</section>
