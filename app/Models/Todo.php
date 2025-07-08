<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'completed',
        'user_id'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'context_data' => 'array'
    ];

    public function files(): HasMany
    {
        return $this->hasMany(TodoFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
