<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function freelances()
    {
        return $this->belongsToMany(Freelance::class, 'freelance_skills');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skills');
    }
}
