<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\Question;

class ReadAloudTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Easy Test
        $test1 = Test::create([
            'title' => 'The Quick Brown Fox',
            'description' => 'A standard, short phonetic pangram test designed to check simple vocalizations and tone pacing.',
            'type' => 'READ_ALOUD',
            'is_active' => true,
        ]);

        $section1 = TestSection::create([
            'test_id' => $test1->id,
            'title' => 'Pangram Reading',
            'sort_order' => 1,
        ]);

        Question::create([
            'test_id' => $test1->id,
            'test_section_id' => $section1->id,
            'question_text' => 'The quick brown fox jumps over the lazy dog. This pangram contains every letter of the English alphabet, making it perfect for voice testing.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1,
        ]);

        // 2. Medium Test
        $test2 = Test::create([
            'title' => 'Technology and Society',
            'description' => 'A medium difficulty reading passage describing the impact of technology on communication and connection.',
            'type' => 'READ_ALOUD',
            'is_active' => true,
        ]);

        $section2 = TestSection::create([
            'test_id' => $test2->id,
            'title' => 'Society Reading',
            'sort_order' => 1,
        ]);

        Question::create([
            'test_id' => $test2->id,
            'test_section_id' => $section2->id,
            'question_text' => 'Modern technology has completely revolutionized the way human beings connect with each other. While it has bridged physical distances and made global communication instantaneous, it has also introduced new challenges regarding isolation and screen dependency.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1,
        ]);

        // 3. Hard Test
        $test3 = Test::create([
            'title' => 'Deep Learning & AI',
            'description' => 'An advanced reading passage that includes complex technical vocabulary and compound sentences.',
            'type' => 'READ_ALOUD',
            'is_active' => true,
        ]);

        $section3 = TestSection::create([
            'test_id' => $test3->id,
            'title' => 'AI Reading',
            'sort_order' => 1,
        ]);

        Question::create([
            'test_id' => $test3->id,
            'test_section_id' => $section3->id,
            'question_text' => 'Artificial intelligence and deep neural networks are transforming computational biology, quantum chemistry, and automated system synthesis. Designing algorithms that accurately replicate human reasoning requires significant mathematical optimization and scalable reinforcement learning architectures.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1,
        ]);
    }
}
