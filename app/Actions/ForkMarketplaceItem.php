<?php

namespace App\Actions;

use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForkMarketplaceItem
{
    public function __construct(private readonly CopyMarketplaceItem $copy) {}

    public function __invoke(MarketplaceItem $item, User $user): Prompt|Benchmark
    {
        return DB::transaction(function () use ($item, $user) {
            $copy = ($this->copy)($item, $user, [
                'name' => $item->title.' (Fork)',
                'description' => $item->summary,
                'changelog' => "Forked from \"{$item->title}\"",
            ]);

            $forkKey = $copy instanceof Prompt ? 'forked_prompt_id' : 'forked_benchmark_id';

            $item->forkedBy()->attach($user->id, [$forkKey => $copy->id]);
            $item->increment('forks_count');

            return $copy;
        });
    }
}
