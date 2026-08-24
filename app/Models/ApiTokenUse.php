<?php

namespace App\Models;

use Database\Factories\ApiTokenUseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenUse extends Model
{
    /** @use HasFactory<ApiTokenUseFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'token_id',
        'method',
        'path',
        'status',
        'duration_ms',
        'tokens_used',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'duration_ms' => 'integer',
            'tokens_used' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'token_id');
    }
}
