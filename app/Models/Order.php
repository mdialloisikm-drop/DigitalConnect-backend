<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'delivered_at' => 'date',
    ];

    /**
     * L'offre sélectionnée pour cette commande
     */
    public function serviceOffer()
    {
        return $this->belongsTo(ServiceOffer::class);
    }

    /**
     * Le service commandé (via l'offre)
     */
    public function service()
    {
        return $this->hasOneThrough(
            Service::class,
            ServiceOffer::class,
            'id',
            'id',
            'service_offer_id',
            'service_id'
        );
    }

    /**
     * Le client qui a passé la commande
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Le freelance qui doit réaliser la commande
     */
    /**
     * Le freelance qui doit réaliser la commande (via service → freelance)
     */
    public function freelance()
    {
        return $this->hasOneThrough(
            Freelance::class,
            ServiceOffer::class,
            'id',
            'id',
            'service_offer_id',
            'freelance_id'
        )->join('services', 'services.id', '=', 'service_offers.service_id');
    }

    /**
     * ✅ ALTERNATIVE PLUS SIMPLE pour freelance
     */
    public function getFreelanceAttribute()
    {
        return $this->serviceOffer->service->freelance ?? null;
    }

    /**
     * Les livrables de cette commande (polymorphe)
     */
    public function deliverables()
    {
        return $this->morphMany(Attachement::class, 'attachable')
            ->where('file_type', 'deliverable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Les pièces jointes additionnelles (instructions, etc.)
     */
    public function attachments()
    {
        return $this->morphMany(Attachement::class, 'attachable')
            ->where('file_type', 'attachment')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Vérifier si la commande est terminée
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si la commande est en cours
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifier si la commande est livrée
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Vérifier si la commande nécessite une révision
     */
    public function needsRevision()
    {
        return $this->status === 'revision';
    }

    /**
     * Vérifier si la commande est annulée
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
