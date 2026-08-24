<?php

namespace App\Actions;

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForkMarketplaceItem
{
    public function __invoke(MarketplaceItem $item, User $user): Prompt|Benchmark
    {
        return DB::transaction(function () use ($item, $user) {
            $source = $item->item();

            if ($item->item_type === MarketplaceItemType::Prompt) {
                /** @var Prompt $source */
                $prompt = $user->prompts()->create([
                    'name' => $item->title.' (Fork)',
                    'description' => $item->summary,
                    'category' => $source->category,
                    'visibility' => 'private',
                ]);

                $version = $prompt->versions()->create([
                    'version' => 1,
                    'content' => $source->currentContent() ?? $item->snapshot['content'] ?? '',
                    'changelog' => "Forked from \"{$item->title}\"",
                    'created_at' => now(),
                ]);

                $prompt->update(['current_version_id' => $version->id]);

                $item->forkedBy()->attach($user->id, ['forked_prompt_id' => $prompt->id]);
                $item->increment('forks_count');

                return $prompt;
            }

            /** @var Benchmark $source */
            $benchmark = $user->benchmarks()->create([
                'name' => $item->title.' (Fork)',
                'description' => $item->summary,
                'category' => $source->category,
                'visibility' => 'private',
            ]);

            foreach ($source->cases as $case) {
                $newCase = $benchmark->cases()->create($case->only(['title', 'input', 'weight', 'position']));
                foreach ($case->criteria as $criterion) {
                    $newCase->criteria()->create($criterion->only(['type', 'label', 'config', 'position']));
                }
            }

            $item->forkedBy()->attach($user->id, ['forked_benchmark_id' => $benchmark->id]);
            $item->increment('forks_count');

            return $benchmark;
        });
    }
}
