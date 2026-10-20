<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'github_url',
        'live_url',
        'tech_stack',
        'views',
    ];

protected $casts = [
    'tech_stack' => 'array',
];


    public function dailyViews()
{
    return $this->hasMany(ProjectView::class, 'project_id');
}


}

