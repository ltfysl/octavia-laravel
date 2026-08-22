<?php

namespace App\Models;

use App\Enums\BenchmarkCategory;
use App\Enums\Visibility;
use Database\Factories\BenchmarkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Benchmark extends Model
{
    /** @use HasFactory<BenchmarkFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'category' => BenchmarkCategory::class,
            'visibility' => Visibility::class,
            'version' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(BenchmarkCase::class)->orderBy('position');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(Run::class);
    }

    public function marketplaceItem(): HasOne
    {
        return $this->hasOne(MarketplaceItem::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user) {
            $mateIds = $user->teamMateIds();

            $q->whereIn('user_id', $mateIds)
                ->orWhere('visibility', 'public');
        });
    }

    public function caseCount(): int
    {
        return $this->cases()->count();
    }

    public function bumpVersion(): void
    {
        $this->increment('version');
    }
}
