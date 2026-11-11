<?php

namespace App\Http\Controllers;

use App\Models\Resolution;
use App\Services\LegislationImporter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResolutionController extends Controller
{
    // List + search/filter
    public function index(Request $request)
    {
        $filters = $request->validate([
            'number'      => ['nullable', 'string', 'max:50'],
            'title'       => ['nullable', 'string', 'max:255'],
            'subject'     => ['nullable', 'string', 'max:255'],
            'date_passed' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $resolutions = Resolution::filterBy($filters)
            ->latestFirst()
            ->paginate(10)
            ->withQueryString();   // keeps the search values when changing pages

        return view('resolutions.index', compact('resolutions'));
    }

    public function create()
    {
        return view('resolutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        Resolution::create($validated);

        return redirect()->route('admin.resolutions.index')
            ->with('success', 'The resolution was added successfully.');
    }

    public function show(Resolution $resolution)
    {
        return view('resolutions.show', compact('resolution'));
    }

    public function edit(Resolution $resolution)
    {
        return view('resolutions.edit', compact('resolution'));
    }

    public function update(Request $request, Resolution $resolution)
    {
        $validated = $request->validate($this->rules($resolution), $this->messages(), $this->attributes());

        $resolution->update($validated);

        return redirect()->route('admin.resolutions.show', $resolution)
            ->with('success', 'The resolution was updated successfully.');
    }

    public function destroy(Resolution $resolution)
    {
        $resolution->delete();

        return redirect()->route('admin.resolutions.index')
            ->with('success', 'The resolution was deleted.');
    }

    // ---------- Batch import (CSV / Excel) ----------

    public function importForm()
    {
        return view('resolutions.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ], [
            'file.required' => 'Please choose a CSV or Excel file to import.',
            'file.file'     => 'The upload failed. Please try again.',
            'file.max'      => 'The file may not be larger than 5 MB.',
        ]);

        $result = $this->importer()->import($request->file('file'));

        if ($result['fileError']) {
            return back()->withErrors(['file' => $result['fileError']]);
        }

        if (count($result['errors']) > 0) {
            return back()->with('import_errors', $result['errors']);
        }

        return redirect()->route('admin.resolutions.index')
            ->with('success', $result['imported'] . ' resolution(s) imported successfully.');
    }

    public function importTemplate()
    {
        return $this->importer()->downloadTemplate();
    }

    private function importer(): LegislationImporter
    {
        return new LegislationImporter(Resolution::class, 'resolution_number', 'resolution');
    }

    // Validation rules (the same for create and update).
    // On update, the record's own number is ignored by the unique check.
    private function rules(?Resolution $resolution = null): array
    {
        return [
            'resolution_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('resolutions', 'resolution_number')->ignore($resolution?->id),
            ],
            'title'            => ['required', 'string', 'max:500'],
            'subject'          => ['required', 'string', 'max:255'],
            'date_passed'      => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'author_sponsor'   => ['required', 'string', 'max:255'],
            'summary'          => ['nullable', 'string', 'max:5000'],
        ];
    }

    private function messages(): array
    {
        return [
            'resolution_number.unique'     => 'This resolution number already exists. Resolution numbers must be unique.',
            'date_passed.date'            => 'Please enter a valid date.',
            'date_passed.date_format'     => 'Please enter a valid date.',
            'date_passed.before_or_equal' => 'The date passed cannot be in the future.',
        ];
    }

    private function attributes(): array
    {
        return [
            'resolution_number' => 'resolution number',
            'date_passed'      => 'date passed',
            'author_sponsor'   => 'author/sponsor',
        ];
    }
}
