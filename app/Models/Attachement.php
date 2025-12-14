<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Accesseur pour obtenir l'URL complète du fichier
     */
    public function getFileUrlAttribute()
    {
        // Si c'est un lien externe
        if ($this->format === 'link' && $this->url) {
            return $this->url;
        }

        // Si file_url existe déjà en base, le retourner
        if (isset($this->attributes['file_url']) && $this->attributes['file_url']) {
            return $this->attributes['file_url'];
        }

        // Sinon, générer l'URL S3 à partir du path
        if ($this->file_path) {
            return Storage::disk('s3')->url($this->file_path);
        }

        return null;
    }

    protected $appends = ['file_url'];
}
