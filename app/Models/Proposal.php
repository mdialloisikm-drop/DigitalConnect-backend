<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function freelance()
    {
        return $this->belongsTo(Freelance::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachement::class, 'attachable');
    }
}
