<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['task', 'task_time', 'is_completed', 'user_id'];

    protected $casts = [
        'task_time' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function isCompleted(): bool
    {
        return (bool) $this->is_completed;
    }

    public function isPending(): bool
    {
        return ! $this->is_completed;
    }
}
