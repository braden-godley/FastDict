<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Word;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->seedWords();
    }

    private function seedWords(): void
    {
        // Load file
        $filePath = resource_path('words_alpha.txt');
        $fileContents = file_get_contents($filePath);
        $words = explode("\n", $fileContents);

        // Set a batch size that won’t kill your memory but speeds up the insert.
        // You can adjust this if needed.
        $batchSize = 5000;
        $batchData = [];

        foreach ($words as $word) {
            // Build your insert array
            $batchData[] = ['word' => trim($word)];

            // Once we reach the batch size, insert and reset the array
            if (count($batchData) >= $batchSize) {
                Word::insert($batchData);
                $batchData = [];
            }
        }

        // Insert remaining records if they are less than the batch size
        if (! empty($batchData)) {
            Word::insert($batchData);
        }
    }
}
