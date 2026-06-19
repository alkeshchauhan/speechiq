<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\Question;

class AiInterviewTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Software Engineer Technical Interview
        $test1 = Test::create([
            'title' => 'Software Engineer Technical Interview',
            'description' => 'A rigorous AI-driven simulation of a technical software engineering interview. Topics include system scaling, architecture, REST API design, and team collaboration.',
            'type' => 'AI_INTERVIEW',
            'is_active' => true,
        ]);

        $section1 = TestSection::create([
            'test_id' => $test1->id,
            'title' => 'General Technical Architecture',
            'sort_order' => 1,
        ]);

        // Standard baseline prompt/starting instruction
        Question::create([
            'test_id' => $test1->id,
            'test_section_id' => $section1->id,
            'question_text' => 'Software Engineering role. Starting query: Tell me about yourself and your primary technical stack.',
            'question_type' => 'AI_INTERVIEW',
            'sort_order' => 1,
        ]);

        // 2. Client Relations Specialist Conversation Interview
        $test2 = Test::create([
            'title' => 'Client Relations Specialist Interview',
            'description' => 'AI voice assessment testing client communication quality, empathy, conflict resolution, and conversational clarity.',
            'type' => 'AI_INTERVIEW',
            'is_active' => true,
        ]);

        $section2 = TestSection::create([
            'test_id' => $test2->id,
            'title' => 'Empathy & Communication',
            'sort_order' => 1,
        ]);

        Question::create([
            'test_id' => $test2->id,
            'test_section_id' => $section2->id,
            'question_text' => 'Customer Success and Client Relations role. Starting query: How do you approach an upset customer who demands an immediate refund for a product issue?',
            'question_type' => 'AI_INTERVIEW',
            'sort_order' => 1,
        ]);
    }
}
