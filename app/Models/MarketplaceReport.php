<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceReport extends Model
{
    public const REASONS = ['inappropriate', 'spam', 'copyright', 'broken', 'other'];

    protected $fillable = [
        'marketplace_item_id',
        'reporter_id',
        'reason',
        'message',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'marketplace_item_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
