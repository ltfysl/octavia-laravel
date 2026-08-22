<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BenchmarkCollection extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function benchmarks(): BelongsToMany
    {
        return $this->belongsToMany(Benchmark::class, 'benchmark_collection_items', 'collection_id', 'benchmark_id');
    }
}
