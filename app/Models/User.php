<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Canonical list of grantable admin sections. Single source of truth,
     * consumed by the section middleware, the position preset editor, the
     * per-member permission editor, and the seeders.
     */
    public const SECTIONS = [
        'projects',
        'blog',
        'team_management',
        'website_settings',
        'website_contacts',
        'join_requests',
        'email_list',
        'todo',
    ];

    public const SECTIONS_LABELS = [
        'projects' => 'Projects',
        'blog' => 'Blog',
        'team_management' => 'Team Management',
        'website_settings' => 'Website Settings',
        'website_contacts' => 'Website Contacts',
        'join_requests' => 'Join Requests',
        'email_list' => 'E-mails',
        'todo' => 'To Do List',
    ];

    public const ROLES = ['super_admin', 'admin', 'member'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'designation',
        'facebook_url',
        'linkedin_url',
        'github_url',
        'experience',
        'slug',
        'biography',
        'custom_cv',
        'role',
        'status',
        'position_id',
        'permissions',

    ];

    protected $casts = [
        'languages' => 'array',
        'permissions' => 'array',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function emails() {
    return $this->hasMany(Email::class);
}

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Whether this user holds the super admin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check whether this user may access a grantable admin section.
     * Super admins always can; everyone else needs the section granted
     * in their (per-user) permissions list.
     */
    public function canAccess(string $section): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($section, $this->permissions ?? [], true);
    }

public function getRouteKeyName()
{
    return 'slug';
}

    public function isSuspended(): bool
    {
        return in_array(strtolower($this->status ?? 'active'), ['suspended', 'suspanded'], true);
    }

    public function isActive(): bool
    {
        return !$this->isSuspended();
    }
}
