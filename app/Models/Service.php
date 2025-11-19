<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Constantes pour les statuts
     */
    const STATUS_EN_ATTENTE = 'en_attente';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_REJECTED = 'rejected';

    public function freelance()
    {
        return $this->belongsTo(Freelance::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation : Offres du service
     */
    public function offers()
    {
        return $this->hasMany(ServiceOffer::class)->orderByRaw("
            CASE title
                WHEN 'Starter' THEN 1
                WHEN 'Standard' THEN 2
                WHEN 'Advanced' THEN 3
            END
        ");
    }

    /**
     * Vérifier si le service a une seule offre
     */
    public function hasSingleOffer(): bool
    {
        return $this->offers()->count() === 1;
    }

    /**
     * Vérifier si le service a trois offres
     */
    public function hasThreeOffers(): bool
    {
        return $this->offers()->count() === 3;
    }

    /**
     * Récupérer l'offre Starter
     */
    public function getStarterOffer()
    {
        return $this->offers()->where('title', ServiceOffer::TITLE_STARTER)->first();
    }

    /**
     * Récupérer l'offre Standard
     */
    public function getStandardOffer()
    {
        return $this->offers()->where('title', ServiceOffer::TITLE_STANDARD)->first();
    }

    /**
     * Récupérer l'offre Advanced
     */
    public function getAdvancedOffer()
    {
        return $this->offers()->where('title', ServiceOffer::TITLE_ADVANCED)->first();
    }

    /**
     * Scopes pour filtrer par statut
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Vérifier si le service est en attente
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    /**
     * Vérifier si le service est publié
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Vérifier si le service est archivé
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Vérifier si le service est rejeté
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
