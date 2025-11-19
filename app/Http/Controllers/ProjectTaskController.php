<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectTaskFormRequest;
use App\Services\ProjectTaskService;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    protected $taskService;

    public function __construct(ProjectTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Lister toutes les tâches d'un projet
     * GET /api/projects/{project}/tasks?priority=haute&status=pending
     */
    public function index(Request $request, string $projectId)
    {
        try {
            $priority = $request->query('priority');
            $status = $request->query('status');

            $tasks = $this->taskService->index($projectId, $priority, $status);
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Lister les tâches triées par priorité
     * GET /api/projects/{project}/tasks/by-priority?direction=desc
     */
    public function indexByPriority(Request $request, string $projectId)
    {
        try {
            $direction = $request->query('direction', 'desc');
            $tasks = $this->taskService->indexByPriority($projectId, $direction);
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer une nouvelle tâche pour un projet
     * POST /api/projects/{project}/tasks
     */
    public function store(ProjectTaskFormRequest $request, string $projectId)
    {
        try {
            $task = $this->taskService->store($projectId, $request->validated());
            return response()->json([
                'message' => 'Tâche créée avec succès',
                'task' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Afficher une tâche spécifique
     * GET /api/tasks/{task}
     */
    public function show(string $taskId)
    {
        try {
            $task = $this->taskService->show($taskId);
            return response()->json($task, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Modifier une tâche
     * PUT/PATCH /api/tasks/{task}
     */
    public function update(ProjectTaskFormRequest $request, string $taskId)
    {
        try {
            $task = $this->taskService->update($taskId, $request->validated());
            return response()->json([
                'message' => 'Tâche modifiée avec succès',
                'task' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Supprimer une tâche
     * DELETE /api/tasks/{task}
     */
    public function destroy(string $taskId)
    {
        try {
            $this->taskService->destroy($taskId);
            return response()->json([
                'message' => 'Tâche supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Marquer une tâche comme complétée
     * POST /api/tasks/{task}/complete
     */
    public function complete(string $taskId)
    {
        try {
            $result = $this->taskService->complete($taskId);
            return response()->json([
                'message' => 'Tâche marquée comme complétée',
                'task' => $result['task'],
                'project_progress' => $result['project_progress']
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Changer le statut d'une tâche
     * POST /api/tasks/{task}/status
     */
    public function changeStatus(Request $request, string $taskId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            $result = $this->taskService->changeStatus($taskId, $request->status);
            return response()->json([
                'message' => 'Statut de la tâche modifié avec succès',
                'task' => $result['task'],
                'project_progress' => $result['project_progress']
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Changer la priorité d'une tâche
     * POST /api/tasks/{task}/priority
     */
    public function changePriority(Request $request, string $taskId)
    {
        $request->validate([
            'priority' => 'required|in:basse,moyenne,haute,urgente'
        ]);

        try {
            $task = $this->taskService->changePriority($taskId, $request->priority);
            return response()->json([
                'message' => 'Priorité de la tâche modifiée avec succès',
                'task' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Réorganiser les tâches d'un projet
     * POST /api/projects/{project}/tasks/reorder
     */
    public function reorder(Request $request, string $projectId)
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'integer|exists:project_tasks,id'
        ]);

        try {
            $tasks = $this->taskService->reorder($projectId, $request->task_ids);
            return response()->json([
                'message' => 'Tâches réorganisées avec succès',
                'tasks' => $tasks
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Obtenir les statistiques des tâches d'un projet
     * GET /api/projects/{project}/tasks/statistics
     */
    public function statistics(string $projectId)
    {
        try {
            $statistics = $this->taskService->getTaskStatistics($projectId);
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
