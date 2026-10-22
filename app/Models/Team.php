<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function activeUsers()
    {
        return $this->belongsToMany(User::class)
            ->whereNull('users.deleted_at')
            ->where(function ($q) {
                $q->where('users.status', 'active')
                  ->orWhereNull('users.status');
            });
    }

    public function getActiveMembersCountAttribute(): int
    {
        if (isset($this->attributes['active_members_count'])) {
            return (int) $this->attributes['active_members_count'];
        }
        if (isset($this->attributes['active_users_count'])) {
            return (int) $this->attributes['active_users_count'];
        }
        if ($this->relationLoaded('users')) {
            return $this->users->filter(fn ($u) => ! ($u->trashed ?? false) && (! method_exists($u, 'isSuspended') || ! $u->isSuspended()))->count();
        }
        return $this->activeUsers()->count();
    }
}
