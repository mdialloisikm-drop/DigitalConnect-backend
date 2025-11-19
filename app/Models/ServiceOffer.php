<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'delivery_days' => 'integer',
        'number_of_revisions' => 'integer',
    ];

    const TITLE_STARTER = 'Starter';
    const TITLE_STANDARD = 'Standard';
    const TITLE_ADVANCED = 'Advanced';

    /**
     * Relation : Service parent
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * ✅ Relation : Commandes utilisant cette offre
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Vérifier si l'offre est de type Starter
     */
    public function isStarter(): bool
    {
        return $this->title === self::TITLE_STARTER;
    }

    /**
     * Vérifier si l'offre est de type Standard
     */
    public function isStandard(): bool
    {
        return $this->title === self::TITLE_STANDARD;
    }

    /**
     * Vérifier si l'offre est de type Advanced
     */
    public function isAdvanced(): bool
    {
        return $this->title === self::TITLE_ADVANCED;
    }
}
