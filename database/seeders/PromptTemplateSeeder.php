<?php

namespace Database\Seeders;

use App\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class PromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Marketing tagline writer',
                'description' => 'Writes short, punchy product taglines under 8 words.',
                'category' => 'marketing',
                'difficulty' => 'beginner',
                'tags' => 'marketing, tagline, copywriting',
                'body' => "You are a marketing copywriter.\nWrite a punchy product tagline under 8 words for the product described by the user.",
                'example_use_cases' => 'Eco-friendly water bottle, project-management SaaS, hand-crafted furniture.',
                'recommended_benchmark_type' => 'contains',
            ],
            [
                'name' => 'Support ticket summary',
                'description' => 'Summarizes a customer support ticket in one sentence.',
                'category' => 'support',
                'difficulty' => 'medium',
                'tags' => 'support, summary, customer',
                'body' => "You are a support lead.\nSummarize the customer message in one sentence, including the issue and the desired outcome.",
                'example_use_cases' => 'Refund request, feature question, outage report.',
                'recommended_benchmark_type' => 'contains',
            ],
            [
                'name' => 'Code review assistant',
                'description' => 'Reviews a code snippet for bugs and style issues.',
                'category' => 'coding',
                'difficulty' => 'advanced',
                'tags' => 'code, review, bugs',
                'body' => "You are a senior engineer doing code review.\nReview the code snippet. List any bugs, style issues, and one concrete improvement.",
                'example_use_cases' => 'Recursive function, SQL query, React component.',
                'recommended_benchmark_type' => 'regex',
            ],
        ];

        foreach ($templates as $template) {
            PromptTemplate::firstOrCreate(['name' => $template['name']], $template);
        }
    }
}
