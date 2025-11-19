<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFormRequest;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Liste tous les projets (admin uniquement)
     */
    public function index()
    {
        $projects = $this->projectService->index();
        return response()->json($projects, 200);
    }

    /**
     * Liste les projets ouverts (visible par tous)
     * Seuls les projets avec status = 'open' sont visibles
     */
    public function openProjects()
    {
        try {
            $projects = $this->projectService->getOpenProjects();
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Liste les projets en attente de modération (admin uniquement)
     */
    public function pendingProjects()
    {
        try {
            $projects = $this->projectService->getPendingProjects();
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Mes projets (client connecté)
     */
    public function myProjects()
    {
        try {
            $projects = $this->projectService->myProjects();
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer un projet (client)
     * Le projet sera créé avec status = 'en_attente'
     */
    public function store(ProjectFormRequest $request)
    {
        try {
            $project = $this->projectService->store($request->validated());
            return response()->json([
                'message' => 'Projet créé avec succès. Il sera visible après validation par l\'administrateur.',
                'project' => $project
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un projet spécifique
     */
    public function show(string $id)
    {
        $project = $this->projectService->show($id);
        return response()->json($project, 200);
    }

    /**
     * Modifier un projet (client propriétaire uniquement)
     * Seulement si le projet est en statut 'en_attente'
     */
    public function update(ProjectFormRequest $request, string $id)
    {
        try {
            // Validation manuelle pour la mise à jour
            $validatedData = $request->validated();

            $project = $this->projectService->update($validatedData, $id);
            return response()->json([
                'message' => 'Projet modifié avec succès',
                'project' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 403);
        }
    }

    /**
     * Supprimer un projet (client propriétaire uniquement)
     * Seulement si le projet est en statut 'en_attente'
     */
    public function destroy(string $id)
    {
        try {
            $this->projectService->destroy($id);
            return response()->json([
                'message' => 'Projet supprimé avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Approuver un projet (admin uniquement)
     * Change le statut de 'en_attente' à 'open'
     */
    public function approve(string $id)
    {
        try {
            $project = $this->projectService->approve($id);
            return response()->json([
                'message' => 'Projet approuvé avec succès. Il est maintenant visible par les freelances.',
                'project' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Rejeter un projet (admin uniquement)
     * Change le statut de 'en_attente' à 'cancelled'
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000'
        ]);

        try {
            $project = $this->projectService->reject($id, $request->reason);
            return response()->json([
                'message' => 'Projet rejeté',
                'project' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Archiver un projet
     */
    public function archive(string $id)
    {
        try {
            $project = $this->projectService->archive($id);
            return response()->json([
                'message' => 'Projet archivé avec succès',
                'project' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Obtenir les propositions d'un projet
     */
    public function proposals(string $id)
    {
        try {
            $proposals = $this->projectService->getProposals($id);
            return response()->json($proposals, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Attacher des compétences à un projet
     */
    public function attachSkills(Request $request, string $id)
    {
        $request->validate([
            'skill_ids' => 'required|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        try {
            $project = $this->projectService->attachSkills($id, $request->skill_ids);
            return response()->json($project, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Détacher des compétences d'un projet
     */
    public function detachSkills(Request $request, string $id)
    {
        $request->validate([
            'skill_ids' => 'required|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        try {
            $project = $this->projectService->detachSkills($id, $request->skill_ids);
            return response()->json($project, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Obtenir les statistiques des projets (admin)
     */
    public function statistics()
    {
        try {
            $statistics = $this->projectService->getStatistics();
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
