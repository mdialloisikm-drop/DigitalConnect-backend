<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Accesseur pour obtenir l'URL complète de l'image
     */
    public function getImageUrlAttribute()
    {
        // Si image_url existe déjà en base, le retourner
        if (isset($this->attributes['image_url']) && $this->attributes['image_url']) {
            return $this->attributes['image_url'];
        }

        // Sinon, générer l'URL S3 à partir du path
        if ($this->image_path) {
            return Storage::disk('s3')->url($this->image_path);
        }

        return null;
    }

    /**
     * Ajouter image_url aux attributs sérialisés
     */
    protected $appends = ['image_url'];
}
