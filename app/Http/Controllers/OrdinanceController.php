<?php

namespace App\Http\Controllers;

use App\Models\Ordinance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdinanceController extends Controller
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

        $ordinances = Ordinance::filterBy($filters)
            ->latestFirst()
            ->paginate(10)
            ->withQueryString();   // keeps the search values when changing pages

        return view('ordinances.index', compact('ordinances'));
    }

    public function create()
    {
        return view('ordinances.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        Ordinance::create($validated);

        return redirect()->route('admin.ordinances.index')
            ->with('success', 'The ordinance was added successfully.');
    }

    public function show(Ordinance $ordinance)
    {
        return view('ordinances.show', compact('ordinance'));
    }

    public function edit(Ordinance $ordinance)
    {
        return view('ordinances.edit', compact('ordinance'));
    }

    public function update(Request $request, Ordinance $ordinance)
    {
        $validated = $request->validate($this->rules($ordinance), $this->messages(), $this->attributes());

        $ordinance->update($validated);

        return redirect()->route('admin.ordinances.show', $ordinance)
            ->with('success', 'The ordinance was updated successfully.');
    }

    public function destroy(Ordinance $ordinance)
    {
        $ordinance->delete();

        return redirect()->route('admin.ordinances.index')
            ->with('success', 'The ordinance was deleted.');
    }

    // Validation rules (the same for create and update).
    // On update, the record's own number is ignored by the unique check.
    private function rules(?Ordinance $ordinance = null): array
    {
        return [
            'ordinance_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ordinances', 'ordinance_number')->ignore($ordinance?->id),
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
            'ordinance_number.unique'     => 'This ordinance number already exists. Ordinance numbers must be unique.',
            'date_passed.date'            => 'Please enter a valid date.',
            'date_passed.date_format'     => 'Please enter a valid date.',
            'date_passed.before_or_equal' => 'The date passed cannot be in the future.',
        ];
    }

    private function attributes(): array
    {
        return [
            'ordinance_number' => 'ordinance number',
            'date_passed'      => 'date passed',
            'author_sponsor'   => 'author/sponsor',
        ];
    }
}
