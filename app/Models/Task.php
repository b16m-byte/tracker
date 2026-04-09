<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'quadrant',
        'category_id',
        'due_date',
        'completed',
        'completed_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeInQuadrant($query, string $quadrant)
    {
        return $query->where('quadrant', $quadrant);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && !$this->completed && $this->due_date->isPast();
    }
}
