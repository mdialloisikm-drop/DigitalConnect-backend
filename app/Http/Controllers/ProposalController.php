<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalFormRequest;
use App\Models\Proposal;
use App\Services\NotificationService;
use App\Services\ProposalService;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    protected $proposalService;

    public function __construct(ProposalService $proposalService)
    {
        $this->proposalService = $proposalService;
    }

    /**
     * Liste de toutes les propositions (admin uniquement)
     */
    public function index()
    {
        try {
            $proposals = $this->proposalService->index();
            return response()->json($proposals, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mes propositions (freelance connecté)
     */
    public function myProposals()
    {
        try {
            $proposals = $this->proposalService->myProposals();
            return response()->json($proposals, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer une proposition pour un projet
     */
    public function store(ProposalFormRequest $request)
    {
        try {
            $proposal = $this->proposalService->store($request);
            // Envoyer notification au client
            $notificationService = app(NotificationService::class);
            $project = $proposal->project;
            $client = $project->client;
            $freelance = auth()->user()->freelance;

            $notificationService->notifyNewProposal($client, $project, $freelance);
            return response()->json([
                'message' => 'Proposition soumise avec succès',
                'proposal' => $proposal
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Afficher une proposition spécifique
     */
    public function show(string $id)
    {
        try {
            $proposal = $this->proposalService->show($id);
            return response()->json($proposal, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Modifier une proposition
     */
    public function update(ProposalFormRequest $request, string $id)
    {
        try {
            $proposal = $this->proposalService->update($request->validated(), $id);
            return response()->json([
                'message' => 'Proposition modifiée avec succès',
                'proposal' => $proposal
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Supprimer une proposition
     */
    public function destroy(string $id)
    {
        try {
            $this->proposalService->destroy($id);
            return response()->json([
                'message' => 'Proposition supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Accepter une proposition (client uniquement)
     */
    public function accept(string $id)
    {
        try {
            $proposal = $this->proposalService->accept($id);
            // Envoyer notification au freelance
            $notificationService = app(NotificationService::class);
            $notificationService->notifyProposalAccepted($proposal->freelance, $proposal->project);
            return response()->json([
                'message' => 'Proposition acceptée avec succès',
                'proposal' => $proposal
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Rejeter une proposition (client uniquement)
     */
    public function reject(string $id)
    {
        try {
            $proposal = $this->proposalService->reject($id);
            // Envoyer notification au freelance
            $notificationService = app(NotificationService::class);
            $notificationService->notifyProposalRejected($proposal->freelance, $proposal->project);
            return response()->json([
                'message' => 'Proposition rejetée',
                'proposal' => $proposal
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function receivedProposals(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->client->id;

        $proposals = Proposal::whereHas('project', function($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->with(['project', 'freelance.user'])
            ->paginate($request->input('per_page', 10));

        return response()->json($proposals);
    }
}
