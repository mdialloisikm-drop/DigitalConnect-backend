<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'deadline' => 'date',
        'start_date' => 'date',
        'duration' => 'integer',
        'budget' => 'decimal:2',
        'progress' => 'integer'
    ];

    /**
     * Constantes pour les statuts
     */
    const STATUS_EN_ATTENTE = 'en_attente';
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ARCHIVED = 'archived';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skills');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }


    public function attachments()
    {
        return $this->morphMany(Attachement::class, 'attachable');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les tâches
     */
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->orderBy('order');
    }

    /**
     * Calculer et mettre à jour la progression du projet
     */
    public function calculateProgress(): int
    {
        $totalTasks = $this->tasks()->count();

        if ($totalTasks === 0) {
            $progress = 0;
        } else {
            $completedTasks = $this->tasks()->where('status', 'completed')->count();
            $progress = round(($completedTasks / $totalTasks) * 100);
        }

        $this->update(['progress' => $progress]);

        return $progress;
    }

    /**
     * Obtenir le nombre de tâches complétées
     */
    public function getCompletedTasksCount(): int
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    /**
     * Obtenir le nombre total de tâches
     */
    public function getTotalTasksCount(): int
    {
        return $this->tasks()->count();
    }

    /**
     * Vérifier si toutes les tâches sont complétées
     */
    public function allTasksCompleted(): bool
    {
        $totalTasks = $this->getTotalTasksCount();

        if ($totalTasks === 0) {
            return false;
        }

        return $this->getCompletedTasksCount() === $totalTasks;
    }

    /**
     * Scopes pour filtrer par statut
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Vérifier si le projet est en attente de modération
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    /**
     * Vérifier si le projet est ouvert
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Vérifier si le projet est en cours
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Vérifier si le projet est complété
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Vérifier si le projet est annulé
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Vérifier si le projet est archivé
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }
}
