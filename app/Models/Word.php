<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    public static function search(string $query): Collection
    {
        return Word::query()
            ->whereLike('word', $query . "%")
            ->orderByRaw('LENGTH(word) ASC')
            ->take(10)
            ->get();
    }
}
