<?php

namespace App\Models;

use App\Enums\Visibility;
use Database\Factories\PromptFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prompt extends Model
{
    /** @use HasFactory<PromptFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'visibility',
        'current_version_id',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => Visibility::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromptVersion::class)->orderByDesc('version');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'current_version_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(Run::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user) {
            $mateIds = $user->teamMateIds();

            $q->whereIn('user_id', $mateIds)
                ->orWhere('visibility', 'public');
        });
    }

    public function currentContent(): ?string
    {
        return $this->currentVersion?->content;
    }

    public function nextVersionNumber(): int
    {
        return ((int) $this->versions()->max('version')) + 1;
    }
}
