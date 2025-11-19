<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];

    /**
     * Constantes pour les priorités
     */
    const PRIORITY_URGENTE = 'urgente';
    const PRIORITY_HAUTE = 'haute';
    const PRIORITY_MOYENNE = 'moyenne';
    const PRIORITY_BASSE = 'basse';

    /**
     * Relation avec le projet
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Marquer la tâche comme complétée
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ]);
    }

    /**
     * Marquer la tâche comme en cours
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => 'in_progress',
            'completed_at' => null
        ]);
    }

    /**
     * Marquer la tâche comme en attente
     */
    public function markAsPending(): void
    {
        $this->update([
            'status' => 'pending',
            'completed_at' => null
        ]);
    }

    /**
     * Vérifier si la tâche est complétée
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si la tâche est urgente
     */
    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENTE;
    }

    /**
     * Vérifier si la tâche est de haute priorité
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_URGENTE, self::PRIORITY_HAUTE]);
    }

    /**
     * Calculer le pourcentage que représente cette tâche
     */
    public function getTaskPercentage(): float
    {
        $totalTasks = $this->project->tasks()->count();

        if ($totalTasks === 0) {
            return 0;
        }

        return round(100 / $totalTasks, 2);
    }

    /**
     * Obtenir la valeur numérique de la priorité (pour tri)
     */
    public function getPriorityValue(): int
    {
        return match($this->priority) {
            self::PRIORITY_URGENTE => 4,
            self::PRIORITY_HAUTE => 3,
            self::PRIORITY_MOYENNE => 2,
            self::PRIORITY_BASSE => 1,
            default => 0
        };
    }

    /**
     * Scope pour les tâches complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les tâches en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les tâches en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les tâches urgentes
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENTE);
    }

    /**
     * Scope pour les tâches de haute priorité
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HAUTE);
    }

    /**
     * Scope pour les tâches de priorité moyenne
     */
    public function scopeMediumPriority($query)
    {
        return $query->where('priority', self::PRIORITY_MOYENNE);
    }

    /**
     * Scope pour les tâches de basse priorité
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', self::PRIORITY_BASSE);
    }

    /**
     * Scope pour filtrer par priorité
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope pour trier par priorité (du plus urgent au moins urgent)
     */
    public function scopeOrderByPriority($query, string $direction = 'desc')
    {
        return $query->orderByRaw("
            CASE priority
                WHEN 'urgente' THEN 4
                WHEN 'haute' THEN 3
                WHEN 'moyenne' THEN 2
                WHEN 'basse' THEN 1
                ELSE 0
            END {$direction}
        ");
    }
}
