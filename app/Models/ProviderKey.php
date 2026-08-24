<?php

namespace App\Models;

use Database\Factories\ProviderKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderKey extends Model
{
    /** @use HasFactory<ProviderKeyFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function markUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
