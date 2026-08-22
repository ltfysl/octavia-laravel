<?php

namespace App\Models;

use App\Enums\MarketplaceItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceItem extends Model
{
    protected $fillable = [
        'item_type',
        'prompt_id',
        'benchmark_id',
        'publisher_id',
        'title',
        'summary',
        'snapshot',
        'version',
        'featured',
        'downloads',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'item_type' => MarketplaceItemType::class,
            'snapshot' => 'array',
            'version' => 'integer',
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function installs(): HasMany
    {
        return $this->hasMany(MarketplaceInstall::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function benchmark(): BelongsTo
    {
        return $this->belongsTo(Benchmark::class);
    }

    public function scopeListed(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->orderByDesc('published_at');
    }

    public function item(): Prompt|Benchmark|null
    {
        return $this->item_type === MarketplaceItemType::Prompt ? $this->prompt : $this->benchmark;
    }
}
