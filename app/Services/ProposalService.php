<?php

namespace App\Services;

use App\Http\Requests\ProposalFormRequest;
use App\Mail\NewProposalReceivedMail;
use App\Models\Proposal;
use App\Models\Project;
use App\Models\Attachement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProposalService
{

    protected $conversationService;
    protected $contractService;

    public function __construct(ConversationService $conversationService, ContractService $contractService)
    {
        $this->conversationService = $conversationService;
        $this->contractService = $contractService;
    }
    /**
     * Liste de toutes les propositions (admin uniquement)
     */
    public function index()
    {
        return Proposal::with([
            'project.client.user',
            'freelance.user',
            'attachments'
        ])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Mes propositions (freelance connecté)
     */
    public function myProposals()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à leurs propositions.');
        }

        return Proposal::where('freelance_id', $user->freelance->id)
            ->with([
                'project.client.user',
                'project.category',
                'project.skills',
                'attachments'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Créer une proposition
     */
    public function store(ProposalFormRequest $request)
    {
        $user = auth('api')->user();

        // Vérifier que l'utilisateur est un freelance
        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent soumettre des propositions.');
        }

        // Vérifier que le projet existe et est ouvert
        $project = Project::findOrFail($request->project_id);

        if ($project->status !== 'open') {
            throw new \Exception('Ce projet n\'accepte plus de propositions.');
        }

        // Vérifier que le freelance n'a pas déjà soumis une proposition pour ce projet
        $existingProposal = Proposal::where('project_id', $request->project_id)
            ->where('freelance_id', $user->freelance->id)
            ->first();

        if ($existingProposal) {
            throw new \Exception('Vous avez déjà soumis une proposition pour ce projet.');
        }

        DB::beginTransaction();
        try {
            // Créer la proposition
            $data = $request->except('attachments');
            $data['freelance_id'] = $user->freelance->id;
            $data['status'] = 'pending';

            $proposal = Proposal::create($data);

            // Gérer les pièces jointes si présentes
            if ($request->hasFile('attachments')) {
                $this->storeAttachments($proposal, $request->file('attachments'), $user->id);
            }

            DB::commit();

            Mail::to($proposal->project->client->user->email)->send(
                new NewProposalReceivedMail(
                    $proposal->project,
                    $proposal,
                    $proposal->project->client,
                    $proposal->freelance
                )
            );

            Log::info('Email de confirmation envoyé: ' . $proposal->project->client->user->email);

            return $proposal->load([
                'project.client.user',
                'freelance.user',
                'attachments'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Afficher une proposition spécifique
     */
    public function show(string $id)
    {
        $proposal = Proposal::with([
            'project.client.user',
            'project.category',
            'project.skills',
            'freelance.user',
            'freelance.skills',
            'attachments'
        ])->findOrFail($id);

        $user = auth('api')->user();

        // Vérifier les permissions
        if ($user->user_type === 'freelance' && $proposal->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas voir cette proposition.');
        }

        if ($user->user_type === 'client' && $proposal->project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas voir cette proposition.');
        }

        return $proposal;
    }

    /**
     * Mettre à jour une proposition (uniquement si pending)
     */
    public function update(array $data, string $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que la proposition appartient au freelance connecté
        if ($user->user_type === 'freelance' && $proposal->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas modifier cette proposition.');
        }

        // Vérifier que la proposition est encore en attente
        if ($proposal->status !== 'pending') {
            throw new \Exception('Vous ne pouvez modifier que les propositions en attente.');
        }

        $proposal->update(collect($data)->only([
            'cover_letter',
            'proposed_amount',
            'proposed_duration'
        ])->toArray());

        return $proposal->load([
            'project.client.user',
            'freelance.user',
            'attachments'
        ]);
    }

    /**
     * Supprimer une proposition (uniquement si pending)
     */
    public function destroy(string $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que la proposition appartient au freelance connecté
        if ($user->user_type === 'freelance' && $proposal->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas supprimer cette proposition.');
        }

        // Vérifier que la proposition est encore en attente
        if ($proposal->status !== 'pending') {
            throw new \Exception('Vous ne pouvez supprimer que les propositions en attente.');
        }

        // Supprimer les pièces jointes
        foreach ($proposal->attachments as $attachment) {
            if ($attachment->format === 'file' && $attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $proposal->delete();
    }

    /**
     * Accepter une proposition (client uniquement)
     */
    public function accept(string $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que l'utilisateur est le client propriétaire du projet
        if ($user->user_type !== 'client' || $proposal->project->client_id !== $user->client->id) {
            throw new \Exception('Seul le client propriétaire du projet peut accepter des propositions.');
        }

        // Vérifier que la proposition est en attente
        if ($proposal->status !== 'pending') {
            throw new \Exception('Cette proposition ne peut plus être acceptée.');
        }

        // Vérifier qu'il n'y a pas déjà une proposition acceptée pour ce projet
        $existingAccepted = Proposal::where('project_id', $proposal->project_id)
            ->where('status', 'accepted')
            ->exists();

        if ($existingAccepted) {
            throw new \Exception('Une proposition a déjà été acceptée pour ce projet.');
        }

        DB::beginTransaction();
        try {
            // 1. Accepter cette proposition
            $proposal->update(['status' => 'accepted']);

            // 2. Rejeter automatiquement toutes les autres propositions du même projet
            Proposal::where('project_id', $proposal->project_id)
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // 3. Mettre à jour le statut du projet à "in_progress"
            $project = $proposal->project;
            $start_date = now()->toDateString();

            // ✅ CORRECTION : Utiliser la durée proposée par le freelance
            $proposedDuration = $proposal->proposed_duration;

            // Calculer le deadline : date_debut + durée proposée par le freelance
            $deadline = now()->addDays($proposedDuration)->toDateString();

            $project->update([
                'status' => 'in_progress',
                'start_date' => $start_date,
                'deadline' => $deadline,
                'duration' => $proposedDuration  // ✅ Mettre à jour aussi la durée du projet
            ]);

            // 4. Créer ou récupérer la conversation entre le client et le freelance
            $conversation = $this->conversationService->createConversationFromProposal($proposal);

            // 5. GÉNÉRATION AUTOMATIQUE DU CONTRAT
            $contract = $this->contractService->createFromProposal($proposal);

            DB::commit();

            Mail::to($proposal->freelance->user->email)->send(
                new \App\Mail\ProposalAcceptedMail($proposal->project, $proposal, $proposal->freelance)
            );

            Log::info('Email d\'acceptation envoyé avec succès: ' . $proposal->freelance->user->email);

            return $proposal->load([
                'project.client.user',
                'freelance.user',
                'attachments',
                'contract'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Rejeter une proposition (client uniquement)
     */
    public function reject(string $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que l'utilisateur est le client propriétaire du projet
        if ($user->user_type !== 'client' || $proposal->project->client_id !== $user->client->id) {
            throw new \Exception('Seul le client propriétaire du projet peut rejeter des propositions.');
        }

        // Vérifier que la proposition est en attente
        if ($proposal->status !== 'pending') {
            throw new \Exception('Cette proposition ne peut plus être rejetée.');
        }

        $proposal->update(['status' => 'rejected']);

        Mail::to($proposal->freelance->user->email)->send(
            new \App\Mail\ProposalRejectedMail($proposal->project, $proposal, $proposal->freelance)
        );

        Log::info('Email de rejet envoyé avec succès: ' . $proposal->freelance->user->email);


        return $proposal->load([
            'project.client.user',
            'freelance.user',
            'attachments'
        ]);
    }

    /**
     * Stocker les pièces jointes
     */
    private function storeAttachments(Proposal $proposal, array $files, int $uploadedBy)
    {
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store('proposals', 'public');

            Attachement::create([
                'attachable_type' => Proposal::class,
                'attachable_id' => $proposal->id,
                'uploaded_by' => $uploadedBy,
                'file_type' => 'attachment',
                'file_name' => $originalName,
                'file_path' => $path,
                'format' => 'file',
            ]);
        }
    }
}
