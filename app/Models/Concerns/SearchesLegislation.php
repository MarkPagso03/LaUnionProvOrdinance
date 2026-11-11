<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared search code for Ordinance and Resolution.
 * The model that uses this trait must define: protected string $numberColumn
 */
trait SearchesLegislation
{
    /**
     * Admin filter: number (partial), title (partial, every word must match), date_passed (exact).
     */
    public function scopeFilterBy(Builder $query, array $filters): Builder
    {
        if (filled($filters['number'] ?? null)) {
            $query->where($this->numberColumn, 'like', '%' . self::escapeLike($filters['number']) . '%');
        }

        if (filled($filters['title'] ?? null)) {
            foreach (self::splitWords($filters['title']) as $word) {
                $query->where('title', 'like', '%' . self::escapeLike($word) . '%');
            }
        }

        if (filled($filters['subject'] ?? null)) {
            foreach (self::splitWords($filters['subject']) as $word) {
                $query->where('subject', 'like', '%' . self::escapeLike($word) . '%');
            }
        }

        if (filled($filters['date_passed'] ?? null)) {
            $query->whereDate('date_passed', $filters['date_passed']);
        }

        return $query;
    }

    /**
     * Public keyword search: every word must appear in the title, subject or number.
     * "agricultur" matches "Agriculture" because the match is partial.
     */
    public function scopeKeyword(Builder $query, ?string $keyword): Builder
    {
        $numberColumn = $this->numberColumn;

        foreach (self::splitWords((string) $keyword) as $word) {
            $like = '%' . self::escapeLike($word) . '%';

            $query->where(function ($q) use ($numberColumn, $like) {
                $q->where('title', 'like', $like)
                  ->orWhere('subject', 'like', $like)
                  ->orWhere($numberColumn, 'like', $like);
            });
        }

        return $query;
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('date_passed')->orderByDesc('id');
    }

    // $record->number gives the official number for both record types.
    public function getNumberAttribute(): string
    {
        return $this->{$this->numberColumn};
    }

    private static function splitWords(string $text): array
    {
        return preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    }

    // Stops "%" and "_" typed by the user from acting as LIKE wildcards.
    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
