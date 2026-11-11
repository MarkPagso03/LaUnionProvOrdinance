<div class="field">
    <label for="ordinance_number">Ordinance Number <span class="required">*</span></label>
    <input type="text" id="ordinance_number" name="ordinance_number"
           value="{{ old('ordinance_number', $ordinance->ordinance_number) }}"
           class="@error('ordinance_number') is-invalid @enderror">
    @error('ordinance_number')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="title">Title <span class="required">*</span></label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $ordinance->title) }}"
           class="@error('title') is-invalid @enderror">
    @error('title')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="subject">Subject <span class="required">*</span></label>
    <input type="text" id="subject" name="subject"
           value="{{ old('subject', $ordinance->subject) }}"
           placeholder="e.g. Environment, Agriculture, Health"
           class="@error('subject') is-invalid @enderror">
    @error('subject')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="date_passed">Date Passed <span class="required">*</span></label>
    <input type="date" id="date_passed" name="date_passed"
           value="{{ old('date_passed', $ordinance->date_passed?->format('Y-m-d')) }}"
           class="@error('date_passed') is-invalid @enderror">
    @error('date_passed')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="author_sponsor">Author/Sponsor <span class="required">*</span></label>
    <input type="text" id="author_sponsor" name="author_sponsor"
           value="{{ old('author_sponsor', $ordinance->author_sponsor) }}"
           class="@error('author_sponsor') is-invalid @enderror">
    @error('author_sponsor')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label for="summary">Summary / Remarks (optional)</label>
    <textarea id="summary" name="summary" rows="5" class="@error('summary') is-invalid @enderror">{{ old('summary', $ordinance->summary) }}</textarea>
    @error('summary')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>
