<?php

use App\Models\Word;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/words", function () {
    $query = request()->query("query");

    $words = [];
    $definitions = null;

    if ($query !== null) {
        $words = Word::search($query);

        if (count($words) > 0) {
            foreach ($words as $word) {
                $text = $word->word;
                $key = env("MERRIAM_WEBSTER_API_KEY");

                $response = Cache::remember("dictionary.{$text}", 3600, function() use ($text, $key) {
                    $response = Http::get("https://dictionaryapi.com/api/v3/references/collegiate/json/$text?key=$key");
                    return $response->ok() ? $response->json() : null;
                });

                if (is_array($response[0])) {
                    $definitions = $response;
                } else {
                    $words->shift();
                }

                if ($definitions !== null) break;
            }
        }
    }

    return [
        "words" => $words,
        "definitions" => $definitions,
    ];
});
