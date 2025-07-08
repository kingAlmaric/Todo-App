<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoFile extends Model
{
    protected $fillable = [
        'todo_id',
        'filename',
        'original_filename',
        'mime_type',
        'size'
    ];

    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
} 