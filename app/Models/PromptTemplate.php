<?php

namespace App\Models;

use Database\Factories\PromptTemplateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    /** @use HasFactory<PromptTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'difficulty',
        'tags',
        'body',
        'example_use_cases',
        'recommended_benchmark_type',
        'is_custom',
    ];

    protected function casts(): array
    {
        return [
            'is_custom' => 'boolean',
        ];
    }

    public function scopeForCategory(Builder $query, string $category): Builder
    {
        return $category === 'all'
            ? $query
            : $query->where('category', $category);
    }
}
