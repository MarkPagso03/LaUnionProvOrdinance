<?php

namespace App\Http\Controllers;

use App\Models\Ordinance;
use App\Models\Resolution;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'ordinanceCount'    => Ordinance::count(),
            'resolutionCount'   => Resolution::count(),
            'latestOrdinances'  => Ordinance::latestFirst()->take(5)->get(),
            'latestResolutions' => Resolution::latestFirst()->take(5)->get(),
        ]);
    }
}
