<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFormRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * Liste de tous les services publiés (marketplace - visible par tous)
     * ✅ CHANGEMENT : Seulement les services avec status='published'
     */
    public function index(Request $request)
    {
        try {
            // Récupérer le paramètre per_page (défaut : 12, max : 50)
            $perPage = $request->input('per_page', 12);
            $perPage = min($perPage, 50);

            $services = $this->serviceService->getPublishedServices($perPage);
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * ✅ NOUVEAU : Liste des services en attente de validation (admin uniquement)
     */
    public function pendingServices()
    {
        try {
            $services = $this->serviceService->getPendingServices();
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Mes services uniquement (freelance connecté)
     */
    public function myServices(Request $request)
    {
        try {
            // Récupérer le paramètre per_page (défaut : 12, max : 50)
            $perPage = $request->input('per_page', 12);
            $perPage = min($perPage, 50);

            $services = $this->serviceService->myServices($perPage);
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer un nouveau service (statut automatique : en_attente)
     */
    public function store(ServiceFormRequest $request)
    {
        try {
            $service = $this->serviceService->store($request);
            return response()->json([
                'message' => 'Service créé avec succès. Il sera visible après validation par l\'administrateur.',
                'service' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Afficher un service spécifique
     */
    public function show(string $id)
    {
        try {
            $service = $this->serviceService->show($id);
            return response()->json($service, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Modifier un service (freelance propriétaire uniquement ET seulement si en_attente ou rejected)
     */
    public function update(ServiceFormRequest $request, string $id)
    {
        try {
            $service = $this->serviceService->update($request->validated(), $id);
            return response()->json([
                'message' => 'Service modifié avec succès',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Supprimer un service
     */
    public function destroy(string $id)
    {
        try {
            $this->serviceService->destroy($id);
            return response()->json([
                'message' => 'Service supprimé avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * ✅ NOUVEAU : Approuver un service (admin uniquement)
     */
    public function approve(string $id)
    {
        try {
            $service = $this->serviceService->approve($id);
            return response()->json([
                'message' => 'Service approuvé avec succès. Il est maintenant visible sur la marketplace.',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * ✅ NOUVEAU : Rejeter un service (admin uniquement)
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000'
        ]);

        try {
            $service = $this->serviceService->reject($id, $request->reason);
            return response()->json([
                'message' => 'Service rejeté',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * ✅ NOUVEAU : Archiver un service
     */
    public function archive(string $id)
    {
        try {
            $service = $this->serviceService->archive($id);
            return response()->json([
                'message' => 'Service archivé avec succès',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * ✅ NOUVEAU : Obtenir les statistiques des services (admin)
     */
    public function statistics()
    {
        try {
            $statistics = $this->serviceService->getStatistics();
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Ajouter des images à un service
     */
    public function addImages(Request $request, string $id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $service = $this->serviceService->addImages($id, $request->file('images'));
            return response()->json([
                'message' => 'Images ajoutées avec succès',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Supprimer une image d'un service
     */
    public function removeImage(string $id, string $imageId)
    {
        try {
            $service = $this->serviceService->removeImage($id, $imageId);
            return response()->json([
                'message' => 'Image supprimée avec succès',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
