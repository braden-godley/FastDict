<?php

use App\Jobs\DefinitionLookup;
use App\Models\Word;
use App\Services\DictionaryService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/words", function () {
    $query = request()->query("query");

    $words = [];

    if ($query !== null) {
        $words = Word::search($query);

        if ($words->count() > 0) {
            $firstWord = $words[0];
            DefinitionLookup::dispatch($firstWord);
        }
    }

    return [
        "words" => $words,
    ];
});

Route::get("/definitions", function(DictionaryService $ds) {
    $query = request()->query("query");

    $definitions = null;
    if ($query !== null) {
        $definitions = $ds->getDefinitions($query);
    }

    return [
        "definitions" => $definitions,
    ];
});

