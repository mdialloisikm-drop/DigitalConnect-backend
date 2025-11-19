<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Proposal;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContractService
{
    /**
     * Liste de tous les contrats (admin uniquement)
     */
    public function index()
    {
        return Contract::with([
            'project.client.user',
            'project.category',
            'proposal.freelance.user',
            'client.user',
            'freelance.user',
            'attachments'
        ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mes contrats (client ou freelance connecté)
     */
    public function myContracts()
    {
        $user = auth('api')->user();

        if ($user->user_type === 'client') {
            return Contract::where('client_id', $user->client->id)
                ->with([
                    'project.category',
                    'proposal',
                    'freelance.user',
                    'freelance.skills',
                    'attachments'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($user->user_type === 'freelance') {
            return Contract::where('freelance_id', $user->freelance->id)
                ->with([
                    'project.category',
                    'proposal',
                    'client.user',
                    'attachments'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        throw new \Exception('Type d\'utilisateur non autorisé.');
    }

    /**
     * Afficher un contrat spécifique
     */
    public function show(string $id)
    {
        $contract = Contract::with([
            'project.client.user',
            'project.category',
            'project.skills',
            'proposal.freelance.user',
            'proposal.attachments',
            'client.user',
            'freelance.user',
            'freelance.skills',
            'attachments'
        ])->findOrFail($id);

        $user = auth('api')->user();

        // Vérifier les permissions
        if ($user->user_type === 'client' && $contract->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas consulter ce contrat.');
        }

        if ($user->user_type === 'freelance' && $contract->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas consulter ce contrat.');
        }

        return $contract;
    }

    /**
     * Créer automatiquement un contrat à partir d'une proposition acceptée
     * Cette méthode est appelée automatiquement lors de l'acceptation d'une proposition
     */
    public function createFromProposal(Proposal $proposal): Contract
    {
        // Vérifier qu'il n'existe pas déjà un contrat pour cette proposition
        if ($proposal->contract) {
            throw new \Exception('Un contrat existe déjà pour cette proposition.');
        }

        // Vérifier que la proposition est bien acceptée
        if ($proposal->status !== 'accepted') {
            throw new \Exception('Seules les propositions acceptées peuvent générer un contrat.');
        }

        DB::beginTransaction();
        try {
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($proposal->proposed_duration);

            $contract = Contract::create([
                'project_id' => $proposal->project_id,
                'proposal_id' => $proposal->id,
                'client_id' => $proposal->project->client_id,
                'freelance_id' => $proposal->freelance_id,
                'amount' => $proposal->proposed_amount,
                'duration' => $proposal->proposed_duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'terms' => $this->generateContractTerms($proposal),
                'status' => 'active',
                'signed_at' => Carbon::now()
            ]);

            // Mettre à jour le statut du projet à 'in_progress'
            $proposal->project->update(['status' => 'in_progress']);

            DB::commit();

            return $contract->load([
                'project',
                'proposal',
                'client.user',
                'freelance.user'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Générer les termes du contrat de manière automatique
     */
    private function generateContractTerms(Proposal $proposal): string
    {
        $project = $proposal->project;
        $freelance = $proposal->freelance;
        $client = $project->client;

        return "CONTRAT DE PRESTATION DE SERVICES\n\n" .
            "Entre:\n" .
            "Client: {$client->user->full_name}\n" .
            "Freelance: {$freelance->user->full_name}\n\n" .
            "Objet du contrat:\n" .
            "Projet: {$project->title}\n\n" .
            "Description:\n{$project->description}\n\n" .
            "Montant convenu: {$proposal->proposed_amount} €\n" .
            "Durée estimée: {$proposal->proposed_duration} jours\n\n" .
            "Proposition du freelance:\n{$proposal->cover_letter}\n\n" .
            "Date de début: " . Carbon::now()->format('d/m/Y') . "\n" .
            "Date de fin prévue: " . Carbon::now()->addDays($proposal->proposed_duration)->format('d/m/Y') . "\n\n" .
            "Les deux parties s'engagent à respecter les termes de ce contrat.";
    }

    /**
     * Marquer un contrat comme complété
     */
    public function complete(string $id)
    {
        $contract = Contract::findOrFail($id);
        $user = auth('api')->user();

        // Seul le client peut marquer le contrat comme complété
        if ($user->user_type !== 'client' || $contract->client_id !== $user->client->id) {
            throw new \Exception('Seul le client peut marquer ce contrat comme complété.');
        }

        if ($contract->status !== 'active') {
            throw new \Exception('Seuls les contrats actifs peuvent être complétés.');
        }

        DB::beginTransaction();
        try {
            $contract->update([
                'status' => 'completed',
                'end_date' => Carbon::now()
            ]);

            // Mettre à jour le statut du projet à 'completed'
            $contract->project->update(['status' => 'completed']);

            DB::commit();

            return $contract->load([
                'project',
                'proposal',
                'client.user',
                'freelance.user'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Annuler un contrat
     */
    public function cancel(string $id, ?string $reason = null)
    {
        $contract = Contract::findOrFail($id);
        $user = auth('api')->user();

        // Le client ou le freelance peuvent annuler le contrat
        if ($user->user_type === 'client' && $contract->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas annuler ce contrat.');
        }

        if ($user->user_type === 'freelance' && $contract->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas annuler ce contrat.');
        }

        if ($contract->status !== 'active') {
            throw new \Exception('Seuls les contrats actifs peuvent être annulés.');
        }

        DB::beginTransaction();
        try {
            // Ajouter la raison d'annulation aux termes
            if ($reason) {
                $contract->terms .= "\n\nRAISON D'ANNULATION:\n{$reason}\n" .
                    "Annulé par: {$user->full_name}\n" .
                    "Date: " . Carbon::now()->format('d/m/Y H:i');
            }

            $contract->update([
                'status' => 'cancelled',
                'end_date' => Carbon::now()
            ]);

            // Mettre à jour le statut du projet à 'cancelled'
            $contract->project->update(['status' => 'cancelled']);

            DB::commit();

            return $contract->load([
                'project',
                'proposal',
                'client.user',
                'freelance.user'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Marquer un contrat en litige
     */
    public function dispute(string $id, string $disputeReason)
    {
        $contract = Contract::findOrFail($id);
        $user = auth('api')->user();

        // Le client ou le freelance peuvent créer un litige
        if ($user->user_type === 'client' && $contract->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas créer un litige pour ce contrat.');
        }

        if ($user->user_type === 'freelance' && $contract->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas créer un litige pour ce contrat.');
        }

        if ($contract->status !== 'active') {
            throw new \Exception('Seuls les contrats actifs peuvent être marqués en litige.');
        }

        DB::beginTransaction();
        try {
            // Ajouter les détails du litige aux termes
            $contract->terms .= "\n\nLITIGE DÉCLARÉ:\n{$disputeReason}\n" .
                "Déclaré par: {$user->full_name}\n" .
                "Date: " . Carbon::now()->format('d/m/Y H:i');

            $contract->update(['status' => 'disputed']);

            DB::commit();

            return $contract->load([
                'project',
                'proposal',
                'client.user',
                'freelance.user'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Résoudre un litige (admin uniquement)
     */
    public function resolveDispute(string $id, string $resolution, string $newStatus)
    {
        $contract = Contract::findOrFail($id);

        if ($contract->status !== 'disputed') {
            throw new \Exception('Seuls les contrats en litige peuvent être résolus.');
        }

        if (!in_array($newStatus, ['active', 'completed', 'cancelled'])) {
            throw new \Exception('Statut de résolution invalide.');
        }

        DB::beginTransaction();
        try {
            // Ajouter la résolution aux termes
            $contract->terms .= "\n\nRÉSOLUTION DU LITIGE:\n{$resolution}\n" .
                "Date: " . Carbon::now()->format('d/m/Y H:i');

            $contract->update([
                'status' => $newStatus,
                'end_date' => in_array($newStatus, ['completed', 'cancelled']) ? Carbon::now() : $contract->end_date
            ]);

            // Mettre à jour le statut du projet si nécessaire
            if (in_array($newStatus, ['completed', 'cancelled'])) {
                $contract->project->update(['status' => $newStatus]);
            }

            DB::commit();

            return $contract->load([
                'project',
                'proposal',
                'client.user',
                'freelance.user'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtenir les statistiques des contrats
     */
    public function getStatistics()
    {
        $user = auth('api')->user();

        $query = Contract::query();

        if ($user->user_type === 'client') {
            $query->where('client_id', $user->client->id);
        } elseif ($user->user_type === 'freelance') {
            $query->where('freelance_id', $user->freelance->id);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'disputed' => (clone $query)->where('status', 'disputed')->count(),
            'total_amount' => (clone $query)->where('status', 'completed')->sum('amount'),
            'average_duration' => (clone $query)->where('status', 'completed')->avg('duration')
        ];
    }
}
