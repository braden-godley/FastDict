<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DictionaryService
{
    public function getDefinitions(string $word): ?array
    {
        $cacheKey = "dictionary:{$word}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $lockKey = "dictionary:lock:{$word}";
        $lock = Cache::lock($lockKey, 60);

        if ($lock->get()) {
            $definitions = Cache::rememberForever($cacheKey, function () use ($word) {
                $apiKey = env("MERRIAM_WEBSTER_API_KEY");
                $response = Http::get("https://dictionaryapi.com/api/v3/references/collegiate/json/$word?key=$apiKey");
                return $response->ok() ? $response->json() : null;
            });
        } else {
            // If we can’t acquire the lock right away, block for up to 60 seconds
            $lock->block(60);
            $definitions = Cache::get($cacheKey);
        }

        $lock->release();

        return $definitions;
    }
}
