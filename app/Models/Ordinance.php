<?php

namespace App\Models;

use App\Models\Concerns\SearchesLegislation;
use Illuminate\Database\Eloquent\Model;

class Ordinance extends Model
{
    use SearchesLegislation;

    protected $fillable = [
        'ordinance_number',
        'title',
        'subject',
        'date_passed',
        'author_sponsor',
        'summary',
    ];

    protected $casts = [
        'date_passed' => 'date',
    ];

    protected string $numberColumn = 'ordinance_number';
}
