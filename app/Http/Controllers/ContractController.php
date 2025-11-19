<?php

namespace App\Http\Controllers;

use App\Services\ContractService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Liste de tous les contrats (admin uniquement)
     */
    public function index()
    {
        try {
            $contracts = $this->contractService->index();
            return response()->json($contracts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mes contrats (client ou freelance connecté)
     */
    public function myContracts()
    {
        try {
            $contracts = $this->contractService->myContracts();
            return response()->json($contracts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Afficher un contrat spécifique
     */
    public function show(string $id)
    {
        try {
            $contract = $this->contractService->show($id);
            return response()->json($contract, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Marquer un contrat comme complété (client uniquement)
     */
    public function complete(string $id): JsonResponse
    {
        try {
            $contract = $this->contractService->complete($id);
            $notificationService = app(NotificationService::class);
            $freelance = $contract->freelance;
            $project = $contract->project;

            $notificationService->notifyContractCompleted($freelance, $project);
            return response()->json([
                'message' => 'Contrat marqué comme complété avec succès',
                'contract' => $contract
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Annuler un contrat
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000'
        ]);

        try {
            $contract = $this->contractService->cancel($id, $request->reason);
            return response()->json([
                'message' => 'Contrat annulé avec succès',
                'contract' => $contract
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer un litige pour un contrat
     */
    public function dispute(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'dispute_reason' => 'required|string|max:1000'
        ]);

        try {
            $contract = $this->contractService->dispute($id, $request->dispute_reason);
            return response()->json([
                'message' => 'Litige créé avec succès',
                'contract' => $contract
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Résoudre un litige (admin uniquement)
     */
    public function resolveDispute(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|string|max:2000',
            'new_status' => 'required|in:active,completed,cancelled'
        ]);

        try {
            $contract = $this->contractService->resolveDispute(
                $id,
                $request->resolution,
                $request->new_status
            );
            return response()->json([
                'message' => 'Litige résolu avec succès',
                'contract' => $contract
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Obtenir les statistiques des contrats
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->contractService->getStatistics();
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
