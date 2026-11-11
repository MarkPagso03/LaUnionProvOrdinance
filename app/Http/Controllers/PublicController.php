<?php

namespace App\Http\Controllers;

use App\Models\Ordinance;
use App\Models\Resolution;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // Public search page: no login needed, view-only.
    public function index(Request $request)
    {
        // Read the keyword safely (ignore anything that is not plain text).
        $keyword = is_string($request->input('keyword')) ? trim($request->input('keyword')) : '';
        $keyword = mb_substr($keyword, 0, 100);

        // type: all | ordinance | resolution
        $type = $request->input('type');
        if (!in_array($type, ['all', 'ordinance', 'resolution'], true)) {
            $type = 'all';
        }

        $ordinances  = null;
        $resolutions = null;

        if ($type === 'all' || $type === 'ordinance') {
            $ordinances = Ordinance::keyword($keyword)
                ->latestFirst()
                ->paginate(10, ['*'], 'ordinance_page')
                ->withQueryString();
        }

        if ($type === 'all' || $type === 'resolution') {
            $resolutions = Resolution::keyword($keyword)
                ->latestFirst()
                ->paginate(10, ['*'], 'resolution_page')
                ->withQueryString();
        }

        return view('public.index', compact('keyword', 'type', 'ordinances', 'resolutions'));
    }

    public function showOrdinance(Ordinance $ordinance)
    {
        return view('public.show', [
            'record' => $ordinance,
            'label'  => 'Ordinance',
        ]);
    }

    public function showResolution(Resolution $resolution)
    {
        return view('public.show', [
            'record' => $resolution,
            'label'  => 'Resolution',
        ]);
    }
}
