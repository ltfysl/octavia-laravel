<?php

namespace Database\Factories;

use App\Enums\MarketplaceItemType;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MarketplaceItem> */
class MarketplaceItemFactory extends Factory
{
    protected $model = MarketplaceItem::class;

    public function definition(): array
    {
        return [
            'item_type' => MarketplaceItemType::Prompt,
            'publisher_id' => User::factory(),
            'title' => fake()->words(3, true),
            'summary' => fake()->sentence(),
            'snapshot' => ['content' => fake()->paragraph()],
            'version' => 1,
            'published_at' => now(),
            'downloads' => 0,
            'stars_count' => 0,
            'forks_count' => 0,
        ];
    }
}
