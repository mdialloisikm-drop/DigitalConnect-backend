<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProjectTaskService
{
    /**
     * Lister toutes les tâches d'un projet
     */
    public function index(string $projectId, ?string $priority = null, ?string $status = null)
    {
        $project = Project::findOrFail($projectId);

        // Vérifier les permissions
        $this->checkProjectAccess($project);

        $query = $project->tasks()->orderBy('order');

        // Filtrer par priorité si spécifié
        if ($priority) {
            $query->byPriority($priority);
        }

        // Filtrer par statut si spécifié
        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Lister les tâches triées par priorité
     */
    public function indexByPriority(string $projectId, string $direction = 'desc')
    {
        $project = Project::findOrFail($projectId);

        // Vérifier les permissions
        $this->checkProjectAccess($project);

        return $project->tasks()
            ->orderByPriority($direction)
            ->orderBy('order')
            ->get();
    }

    /**
     * Créer une nouvelle tâche (SEUL LE CLIENT peut créer)
     */
    public function store(string $projectId, array $data)
    {
        $project = Project::findOrFail($projectId);

        // Vérifier que c'est le client propriétaire du projet
        $this->checkClientOwnership($project);

        DB::beginTransaction();
        try {
            // Si l'ordre n'est pas spécifié, le mettre à la fin
            if (!isset($data['order'])) {
                $maxOrder = $project->tasks()->max('order') ?? -1;
                $data['order'] = $maxOrder + 1;
            }

            // Si la priorité n'est pas spécifiée, utiliser 'moyenne' par défaut
            if (!isset($data['priority'])) {
                $data['priority'] = 'moyenne';
            }

            // Forcer le statut à 'pending' lors de la création
            $data['status'] = 'pending';

            $task = $project->tasks()->create($data);

            // Recalculer la progression du projet
            $project->calculateProgress();

            DB::commit();

            return $task->load('project');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Afficher une tâche spécifique
     */
    public function show(string $taskId)
    {
        $task = ProjectTask::with('project')->findOrFail($taskId);

        // Vérifier les permissions
        $this->checkProjectAccess($task->project);

        return $task;
    }

    /**
     * Modifier une tâche (SEUL LE CLIENT peut modifier ET seulement si la tâche est en 'pending')
     */
    public function update(string $taskId, array $data)
    {
        $task = ProjectTask::findOrFail($taskId);
        $project = $task->project;

        // Vérifier que c'est le client propriétaire
        $this->checkClientOwnership($project);

        // Vérifier que la tâche est encore en 'pending'
        if ($task->status !== 'pending') {
            throw new \Exception('Vous ne pouvez pas modifier une tâche qui est en cours ou complétée.');
        }

        DB::beginTransaction();
        try {
            // Ne pas permettre la modification du statut via cette méthode
            unset($data['status']);

            $task->update($data);

            DB::commit();

            return $task->load('project');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Supprimer une tâche (SEUL LE CLIENT peut supprimer ET seulement si la tâche est en 'pending')
     */
    public function destroy(string $taskId)
    {
        $task = ProjectTask::findOrFail($taskId);
        $project = $task->project;

        // Vérifier que c'est le client propriétaire
        $this->checkClientOwnership($project);

        // Vérifier que la tâche est encore en 'pending'
        if ($task->status !== 'pending') {
            throw new \Exception('Vous ne pouvez pas supprimer une tâche qui est en cours ou complétée.');
        }

        DB::beginTransaction();
        try {
            $task->delete();

            // Recalculer la progression du projet
            $project->calculateProgress();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Marquer une tâche comme complétée (SEUL LE FREELANCE assigné)
     */
    public function complete(string $taskId)
    {
        $task = ProjectTask::findOrFail($taskId);
        $project = $task->project;

        // Vérifier que c'est le freelance assigné au projet
        $this->checkFreelanceAssignment($project);

        if ($task->status === 'completed') {
            throw new \Exception('Cette tâche est déjà complétée.');
        }

        DB::beginTransaction();
        try {
            $task->markAsCompleted();

            // Recalculer la progression du projet
            $progress = $project->calculateProgress();

            // Si toutes les tâches sont complétées, mettre le projet en attente de validation
            if ($project->allTasksCompleted() && $project->status === 'in_progress') {
                $project->update(['status' => 'en_attente']);
            }

            DB::commit();

            Mail::to($task->project->client->user->email)->send(
                new \App\Mail\TaskCompletedMail(
                    $task->project,
                    $task,
                    $task->project->client,
                    $task->project->contract->freelance
                )
            );

            Log::info('Email d\'acceptation envoyé avec succès: ' . $task->project->client->user->email);

            return [
                'task' => $task->fresh(),
                'project_progress' => $progress
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Changer le statut d'une tâche (SEUL LE FREELANCE assigné)
     */
    public function changeStatus(string $taskId, string $status)
    {
        $task = ProjectTask::findOrFail($taskId);
        $project = $task->project;

        // Vérifier que c'est le freelance assigné
        $this->checkFreelanceAssignment($project);

        if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
            throw new \Exception('Statut invalide.');
        }

        DB::beginTransaction();
        try {
            switch ($status) {
                case 'completed':
                    $task->markAsCompleted();
                    break;
                case 'in_progress':
                    $task->markAsInProgress();
                    break;
                case 'pending':
                    // Ne permettre de repasser en pending que si la tâche était en in_progress
                    if ($task->status !== 'in_progress') {
                        throw new \Exception('Vous ne pouvez repasser une tâche en pending que si elle est en cours.');
                    }
                    $task->markAsPending();
                    break;
            }

            // Recalculer la progression du projet
            $progress = $project->calculateProgress();

            // Si toutes les tâches sont complétées, mettre le projet en attente de validation
            if ($project->allTasksCompleted() && $project->status === 'in_progress') {
                $project->update(['status' => 'en_attente']);
            }

            DB::commit();

            return [
                'task' => $task->fresh(),
                'project_progress' => $progress
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Changer la priorité d'une tâche (SEUL LE CLIENT ET seulement si la tâche est en 'pending')
     */
    public function changePriority(string $taskId, string $priority)
    {
        $task = ProjectTask::findOrFail($taskId);
        $project = $task->project;

        // Vérifier que c'est le client propriétaire
        $this->checkClientOwnership($project);

        // Vérifier que la tâche est encore en 'pending'
        if ($task->status !== 'pending') {
            throw new \Exception('Vous ne pouvez pas modifier la priorité d\'une tâche qui est en cours ou complétée.');
        }

        if (!in_array($priority, ['basse', 'moyenne', 'haute', 'urgente'])) {
            throw new \Exception('Priorité invalide.');
        }

        $task->update(['priority' => $priority]);

        return $task->fresh();
    }

    /**
     * Réorganiser les tâches (SEUL LE CLIENT ET seulement les tâches en 'pending')
     */
    public function reorder(string $projectId, array $taskIds)
    {
        $project = Project::findOrFail($projectId);

        // Vérifier que c'est le client propriétaire
        $this->checkClientOwnership($project);

        DB::beginTransaction();
        try {
            foreach ($taskIds as $index => $taskId) {
                $task = ProjectTask::where('id', $taskId)
                    ->where('project_id', $projectId)
                    ->first();

                if (!$task) {
                    continue;
                }

                // Permettre la réorganisation seulement pour les tâches en pending
                if ($task->status === 'pending') {
                    $task->update(['order' => $index]);
                }
            }

            DB::commit();

            return $project->tasks()->orderBy('order')->get();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtenir les statistiques des tâches d'un projet
     */
    public function getTaskStatistics(string $projectId)
    {
        $project = Project::findOrFail($projectId);

        $this->checkProjectAccess($project);

        $total = $project->tasks()->count();
        $completed = $project->tasks()->completed()->count();
        $inProgress = $project->tasks()->inProgress()->count();
        $pending = $project->tasks()->pending()->count();

        // Statistiques par priorité
        $urgent = $project->tasks()->urgent()->count();
        $highPriority = $project->tasks()->highPriority()->count();
        $mediumPriority = $project->tasks()->mediumPriority()->count();
        $lowPriority = $project->tasks()->lowPriority()->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $pending,
            'progress' => $project->progress,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'by_priority' => [
                'urgente' => $urgent,
                'haute' => $highPriority,
                'moyenne' => $mediumPriority,
                'basse' => $lowPriority,
            ]
        ];
    }

    /**
     * Vérifier l'accès au projet (lecture) - Client, Freelance assigné ou Admin
     */
    private function checkProjectAccess(Project $project)
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Vous devez être authentifié.');
        }

        // Admin a accès à tout
        if ($user->user_type === 'admin') {
            return true;
        }

        // Client propriétaire du projet
        if ($user->user_type === 'client') {
            if ($user->client && $project->client_id === $user->client->id) {
                return true;
            }
        }

        // Freelance assigné via un contrat
        if ($user->user_type === 'freelance') {
            if ($user->freelance) {
                // Vérifier s'il existe un contrat pour ce projet avec ce freelance
                $hasContract = Contract::where('project_id', $project->id)
                    ->where('freelance_id', $user->freelance->id)
                    ->exists();

                if ($hasContract) {
                    return true;
                }
            }
        }

        throw new \Exception('Accès refusé.');
    }

    /**
     * Vérifier que l'utilisateur est le CLIENT propriétaire du projet
     */
    private function checkClientOwnership(Project $project)
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Vous devez être authentifié.');
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        if ($user->user_type !== 'client') {
            throw new \Exception('Seul le client peut effectuer cette action.');
        }

        if ($project->client_id !== $user->client->id) {
            throw new \Exception('Vous n\'êtes pas le propriétaire de ce projet.');
        }

        return true;
    }

    /**
     * Vérifier que l'utilisateur est le FREELANCE assigné au projet via un contrat
     */
    private function checkFreelanceAssignment(Project $project)
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Vous devez être authentifié.');
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seul le freelance assigné peut effectuer cette action.');
        }

        // Vérifier qu'il existe un contrat actif pour ce projet avec ce freelance
        if (!$project->contract) {
            throw new \Exception('Aucun contrat n\'a été établi pour ce projet.');
        }

        if ($project->contract->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous n\'êtes pas le freelance assigné à ce projet.');
        }

        if ($project->contract->status !== 'active') {
            throw new \Exception('Le contrat pour ce projet n\'est pas actif.');
        }

        return true;
    }
}
