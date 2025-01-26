<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DictionaryService
{
    public function getDefinitions(string $word): ?array
    {
        $startTime = microtime(true);
        $definition = $this->__getDefinition($word);
        $endTime = microtime(true);

        Log::info("Got definition", [ "word" => $word, "elapsedTime" => $endTime - $startTime ]);

        return $definition;
    }

    private function __getDefinition(string $word): ?array {
        $cacheKey = "dictionary:{$word}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $lockKey = "dictionary:lock:{$word}";
        $lock = Cache::lock($lockKey, 60);

        if ($lock->get()) {
            try {
                $definitions = Cache::rememberForever($cacheKey, function () use ($word) {
                    $apiKey = env("MERRIAM_WEBSTER_API_KEY");
                    $response = Http::get("https://dictionaryapi.com/api/v3/references/collegiate/json/$word?key=$apiKey");
                    return $response->ok() ? $response->json() : null;
                });
            } finally {
                $lock->release();
            }
        } else {
            // If we can’t acquire the lock right away, block for up to 60 seconds
            $startTime = microtime(true);
            $gotIt = $lock->block(1);
            $endTime = microtime(true);

            Log::info("Waited for existing lookup", [
                "acquiredLock" => $gotIt,
                "elapsedTime"  => $endTime - $startTime,
            ]);

            if ($gotIt) {
                try {
                    // Now we own the lock, but presumably the other process wrote to cache already.
                    $definitions = Cache::get($cacheKey);
                } finally {
                    $lock->release();
                }
            } else {
                // Timed out, never got the lock. We cannot safely release it.
                $definitions = Cache::get($cacheKey);
            }
        }

        return $definitions;
    }
}
