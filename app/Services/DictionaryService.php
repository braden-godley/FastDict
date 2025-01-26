<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $lock->release();
        } else {
            // If we can’t acquire the lock right away, block for up to 60 seconds
            $startTime = microtime(true);
            $lock->block(60);
            $endTime = microtime(true);

            Log::info("Used existing lookup", [ "elapsedTime" => $endTime - $startTime, ]);

            $definitions = Cache::get($cacheKey);
        }


        return $definitions;
    }
}
