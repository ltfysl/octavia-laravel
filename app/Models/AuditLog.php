<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'category',
        'entity_type',
        'entity_id',
        'entity_name',
        'description',
        'metadata',
        'severity',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Convenience writer used by controllers and actions. */
    public static function record(
        string $action,
        string $category,
        string $description,
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $entityName = null,
        string $severity = 'info',
        array $metadata = [],
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'category' => $category,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'description' => $description,
            'metadata' => $metadata ?: null,
            'severity' => $severity,
        ]);
    }
}
