<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSuggestion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'suggested_at',
        'context_data',
        'is_accepted'
    ];

    protected $casts = [
        'context_data' => 'array',
        'suggested_at' => 'datetime',
        'is_accepted' => 'boolean'
    ];

    public function todo()
    {
        return $this->hasOne(Todo::class);
    }
} 