<?php

namespace App\Models;

use App\Models\Concerns\SearchesLegislation;
use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    use SearchesLegislation;

    protected $fillable = [
        'resolution_number',
        'title',
        'subject',
        'date_passed',
        'author_sponsor',
        'summary',
    ];

    protected $casts = [
        'date_passed' => 'date',
    ];

    protected string $numberColumn = 'resolution_number';
}
