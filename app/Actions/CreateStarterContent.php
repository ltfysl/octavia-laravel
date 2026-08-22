<?php

namespace App\Actions;

use App\Models\User;

/**
 * Seeds a new account with a small, genuinely useful starter set:
 * one example prompt and one runnable benchmark, so the first
 * optimization run is a single click away.
 */
class CreateStarterContent
{
    public static function for(User $user): void
    {
        if ($user->prompts()->exists()) {
            return;
        }

        $prompt = $user->prompts()->create([
            'name' => 'Product tagline writer',
            'description' => 'Writes short marketing taglines for a product.',
            'visibility' => 'private',
        ]);

        $version = $prompt->versions()->create([
            'version' => 1,
            'content' => 'You are a marketing assistant. Write a tagline for the product the user describes.',
            'changelog' => 'Initial version',
            'created_at' => now(),
        ]);
        $prompt->update(['current_version_id' => $version->id]);

        $benchmark = $user->benchmarks()->create([
            'name' => 'Tagline quality',
            'description' => 'Checks that taglines are short, on-topic and include the product name.',
            'category' => 'marketing',
            'visibility' => 'private',
        ]);

        $case = $benchmark->cases()->create([
            'title' => 'Eco bottle',
            'input' => 'A reusable water bottle made from recycled steel, brand name: Ecosip.',
            'weight' => 1,
            'position' => 0,
        ]);

        $case->criteria()->create([
            'type' => 'contains',
            'label' => '- Include the brand name (e.g. "Ecosip")',
            'config' => ['values' => ['ecosip']],
            'position' => 0,
        ]);

        $case->criteria()->create([
            'type' => 'llm_judge',
            'label' => '- The response is a single short tagline, not an essay',
            'config' => ['description' => '- The response is a single short tagline, not an essay'],
            'position' => 1,
        ]);

        $case2 = $benchmark->cases()->create([
            'title' => 'Note-taking app',
            'input' => 'A minimal note-taking app for developers, brand name: Inkdown.',
            'weight' => 1,
            'position' => 1,
        ]);

        $case2->criteria()->create([
            'type' => 'contains',
            'label' => '- Include the brand name (e.g. "Inkdown")',
            'config' => ['values' => ['inkdown']],
            'position' => 0,
        ]);
    }
}
