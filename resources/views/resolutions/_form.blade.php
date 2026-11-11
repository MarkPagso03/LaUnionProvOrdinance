<div class="field">
    <label for="resolution_number">Resolution Number <span class="required">*</span></label>
    <input type="text" id="resolution_number" name="resolution_number"
           value="{{ old('resolution_number', $resolution->resolution_number) }}"
           class="@error('resolution_number') is-invalid @enderror">
    @error('resolution_number')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="title">Title <span class="required">*</span></label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $resolution->title) }}"
           class="@error('title') is-invalid @enderror">
    @error('title')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="subject">Subject <span class="required">*</span></label>
    <input type="text" id="subject" name="subject"
           value="{{ old('subject', $resolution->subject) }}"
           placeholder="e.g. Environment, Agriculture, Health"
           class="@error('subject') is-invalid @enderror">
    @error('subject')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="date_passed">Date Passed <span class="required">*</span></label>
    <input type="date" id="date_passed" name="date_passed"
           value="{{ old('date_passed', $resolution->date_passed?->format('Y-m-d')) }}"
           class="@error('date_passed') is-invalid @enderror">
    @error('date_passed')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="author_sponsor">Author/Sponsor <span class="required">*</span></label>
    <input type="text" id="author_sponsor" name="author_sponsor"
           value="{{ old('author_sponsor', $resolution->author_sponsor) }}"
           class="@error('author_sponsor') is-invalid @enderror">
    @error('author_sponsor')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="summary">Summary / Remarks (optional)</label>
    <textarea id="summary" name="summary" rows="5" class="@error('summary') is-invalid @enderror">{{ old('summary', $resolution->summary) }}</textarea>
    @error('summary')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>
