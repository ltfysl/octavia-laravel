<?php

namespace App\Actions;

use App\Enums\MarketplaceItemType;
use App\Models\MarketplaceItem;
use App\Models\User;

class CopyMarketplaceItem
{
    /**
     * Deep-copy a published marketplace item into the user's workspace.
     *
     * @param  array<string, mixed>  $overrides
     */
    public function __invoke(MarketplaceItem $item, User $user, array $overrides = []): object
    {
        $name = $overrides['name'] ?? $item->title;
        $description = $overrides['description'] ?? $item->summary;
        $changelog = $overrides['changelog'] ?? "Installed from marketplace v{$item->version}";

        if ($item->item_type === MarketplaceItemType::Prompt) {
            $copy = $user->prompts()->create([
                'name' => $name,
                'description' => $description,
                'category' => $item->snapshot['category'] ?? $item->prompt?->category ?? 'general',
                'visibility' => 'private',
            ]);

            $content = $item->snapshot['content'] ?? $item->prompt?->currentVersion?->content ?? '';

            $version = $copy->versions()->create([
                'version' => 1,
                'content' => $content,
                'changelog' => $changelog,
                'created_at' => now(),
            ]);

            $copy->update(['current_version_id' => $version->id]);

            return $copy;
        }

        $snapshot = $item->snapshot;
        $source = $item->benchmark;

        $copy = $user->benchmarks()->create([
            'name' => $name,
            'description' => $description,
            'category' => data_get($snapshot, 'category', $source?->category?->value ?? 'general'),
            'visibility' => 'private',
        ]);

        $cases = collect(data_get($snapshot, 'cases', []))
            ->whenEmpty(fn () => $source?->cases()->with('criteria')->get()->map(fn ($case) => [
                'title' => $case->title,
                'input' => $case->input,
                'weight' => (float) $case->weight,
                'position' => $case->position,
                'criteria' => $case->criteria->map(fn ($c) => [
                    'type' => $c->type->value,
                    'label' => $c->label,
                    'config' => $c->config,
                    'position' => $c->position,
                ])->all(),
            ]) ?? collect([]));

        foreach ($cases as $i => $case) {
            $newCase = $copy->cases()->create([
                'title' => $case['title'],
                'input' => $case['input'],
                'weight' => $case['weight'] ?? 1,
                'position' => $case['position'] ?? $i,
            ]);

            foreach ($case['criteria'] ?? [] as $criterion) {
                $newCase->criteria()->create([
                    'type' => $criterion['type'],
                    'label' => $criterion['label'],
                    'config' => $criterion['config'],
                    'position' => $criterion['position'] ?? 0,
                ]);
            }
        }

        return $copy;
    }
}
