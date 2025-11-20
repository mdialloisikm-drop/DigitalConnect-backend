<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class ClientDashboardService
{
    /**
     * Récupérer les statistiques du dashboard client
     * VERSION OPTIMISÉE : 1 seule requête au lieu de charger toutes les données
     */
    public function getStatistics()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à ce dashboard.');
        }

        $clientId = $user->client->id;

        // ✅ OPTIMISATION 1 : Statistiques des projets (1 seule requête SQL)
        $projectStats = Project::where('client_id', $clientId)
            ->selectRaw("
                COUNT(*) as total_projects,
                SUM(CASE WHEN status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as active_projects,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects
            ")
            ->first();

        // ✅ OPTIMISATION 2 : Statistiques des propositions + total dépensé (1 seule requête SQL)
        $proposalStats = Proposal::whereIn('project_id', function($query) use ($clientId) {
            $query->select('id')
                ->from('projects')
                ->where('client_id', $clientId);
        })
            ->selectRaw("
                COUNT(*) as total_proposals,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_proposals,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_proposals,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_proposals
            ")
            ->first();

        // Total dépensé (basé sur les propositions acceptées)
        $totalSpent = Proposal::whereIn('project_id', function($query) use ($clientId) {
            $query->select('id')
                ->from('projects')
                ->where('client_id', $clientId);
        })
            ->where('status', 'accepted')
            ->sum('proposed_amount');

        // ✅ Retourner les statistiques
        return [
            'total_projects' => (int) $projectStats->total_projects ?? 0,
            'active_projects' => (int) $projectStats->active_projects ?? 0,
            'completed_projects' => (int) $projectStats->completed_projects ?? 0,
            'total_spent' => (float) $totalSpent ?? 0.0,
            'total_proposals' => (int) $proposalStats->total_proposals ?? 0,
            'pending_proposals' => (int) $proposalStats->pending_proposals ?? 0,
            'accepted_proposals' => (int) $proposalStats->accepted_proposals ?? 0,
            'rejected_proposals' => (int) $proposalStats->rejected_proposals ?? 0,
        ];
    }

    /**
     * Récupérer les projets récents du client
     */
    public function getRecentProjects(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à ce dashboard.');
        }

        return Project::where('client_id', $user->client->id)
            ->with(['category', 'skills', 'proposals'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les propositions récentes reçues
     */
    public function getRecentProposals(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à ce dashboard.');
        }

        return Proposal::whereIn('project_id', function ($query) use ($user) {
            $query->select('id')
                ->from('projects')
                ->where('client_id', $user->client->id);
        })
            ->with(['freelance.user', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les statistiques détaillées par statut de projet
     */
    public function getProjectStatusBreakdown()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à ce dashboard.');
        }

        return Project::where('client_id', $user->client->id)
            ->selectRaw('
                status,
                COUNT(*) as count,
                SUM(budget) as total_budget
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Récupérer les dépenses mensuelles (12 derniers mois)
     */
    public function getMonthlySpending()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à ce dashboard.');
        }

        return Proposal::whereIn('project_id', function ($query) use ($user) {
            $query->select('id')
                ->from('projects')
                ->where('client_id', $user->client->id);
        })
            ->where('status', 'accepted')
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                SUM(proposed_amount) as total_spent,
                COUNT(*) as proposals_count
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }
}
