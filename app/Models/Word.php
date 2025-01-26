<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Word extends Model
{
    public static function search(string $query): Collection
    {
        return Word::query()
            ->select(['word'])
            ->whereLike('word', $query . "%")
            ->orderByRaw('LENGTH(word) ASC')
            ->take(10)
            ->get()
            ->map(fn ($word) => $word->word);
    }
}
