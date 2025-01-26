<?php

namespace App\Jobs;

use App\Services\DictionaryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DefinitionLookup implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $word,
    ) { }

    /**
     * Execute the job.
     */
    public function handle(DictionaryService $ds): void
    {
        $ds->getDefinitions($this->word);
    }
}
