<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Order;
use App\Models\Proposal;
use App\Models\Contract;

class FreelanceDashboardService
{
    /**
     * Récupérer les statistiques du dashboard freelance
     * VERSION OPTIMISÉE pour PostgreSQL
     */
    public function getStatistics()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        $freelanceId = $user->freelance->id;

        // ✅ OPTIMISATION 1 : Statistiques des services (1 seule requête SQL)
        $serviceStats = Service::where('freelance_id', $freelanceId)
            ->selectRaw("
                COUNT(*) as total_services,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_services,
                SUM(CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END) as pending_services,
                SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived_services
            ")
            ->first();

        // ✅ OPTIMISATION 2 : Statistiques des commandes (via les offres de services)
        $orderStats = Order::whereHas('serviceOffer.service', function ($query) use ($freelanceId) {
            $query->where('freelance_id', $freelanceId);
        })
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status IN ('pending', 'in_progress') THEN 1 ELSE 0 END) as active_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            ")
            ->first();

        // ✅ OPTIMISATION 3 : Statistiques des propositions (1 seule requête SQL)
        $proposalStats = Proposal::where('freelance_id', $freelanceId)
            ->selectRaw("
                COUNT(*) as total_proposals,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_proposals,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_proposals,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_proposals
            ")
            ->first();

        // ✅ OPTIMISATION 4 : Calcul des gains
        // Gains totaux = commandes complétées
        $totalEarnings = Order::whereHas('serviceOffer.service', function ($query) use ($freelanceId) {
            $query->where('freelance_id', $freelanceId);
        })
            ->where('status', 'completed')
            ->sum('amount');

        // Gains en attente = commandes en cours ou livrées
        $pendingEarnings = Order::whereHas('serviceOffer.service', function ($query) use ($freelanceId) {
            $query->where('freelance_id', $freelanceId);
        })
            ->whereIn('status', ['in_progress', 'delivered'])
            ->sum('amount');

        // ✅ OPTIMISATION 5 : Statistiques des contrats
        $contractStats = Contract::where('freelance_id', $freelanceId)
            ->selectRaw("
                COUNT(*) as total_contracts,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_contracts,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_contracts
            ")
            ->first();

        // ✅ Retourner les statistiques
        return [
            // Services
            'total_services' => (int) ($serviceStats->total_services ?? 0),
            'published_services' => (int) ($serviceStats->published_services ?? 0),
            'pending_services' => (int) ($serviceStats->pending_services ?? 0),
            'archived_services' => (int) ($serviceStats->archived_services ?? 0),

            // Commandes
            'total_orders' => (int) ($orderStats->total_orders ?? 0),
            'active_orders' => (int) ($orderStats->active_orders ?? 0),
            'completed_orders' => (int) ($orderStats->completed_orders ?? 0),
            'delivered_orders' => (int) ($orderStats->delivered_orders ?? 0),
            'cancelled_orders' => (int) ($orderStats->cancelled_orders ?? 0),

            // Propositions
            'total_proposals' => (int) ($proposalStats->total_proposals ?? 0),
            'pending_proposals' => (int) ($proposalStats->pending_proposals ?? 0),
            'accepted_proposals' => (int) ($proposalStats->accepted_proposals ?? 0),
            'rejected_proposals' => (int) ($proposalStats->rejected_proposals ?? 0),

            // Contrats
            'total_contracts' => (int) ($contractStats->total_contracts ?? 0),
            'active_contracts' => (int) ($contractStats->active_contracts ?? 0),
            'completed_contracts' => (int) ($contractStats->completed_contracts ?? 0),

            // Gains
            'total_earnings' => (float) ($totalEarnings ?? 0),
            'pending_earnings' => (float) ($pendingEarnings ?? 0),
        ];
    }

    /**
     * Récupérer les services récents du freelance
     */
    public function getRecentServices(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Service::where('freelance_id', $user->freelance->id)
            ->with(['category', 'images', 'offers'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les commandes récentes du freelance
     */
    public function getRecentOrders(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Order::whereHas('serviceOffer.service', function ($query) use ($user) {
            $query->where('freelance_id', $user->freelance->id);
        })
            ->with(['serviceOffer.service', 'client.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les propositions récentes du freelance
     */
    public function getRecentProposals(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Proposal::where('freelance_id', $user->freelance->id)
            ->with(['project.client.user', 'project.category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer la répartition des services par statut
     */
    public function getServiceStatusBreakdown()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Service::where('freelance_id', $user->freelance->id)
            ->selectRaw('
                status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Récupérer la répartition des commandes par statut
     */
    public function getOrderStatusBreakdown()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Order::whereHas('serviceOffer.service', function ($query) use ($user) {
            $query->where('freelance_id', $user->freelance->id);
        })
            ->selectRaw('
                status,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Récupérer les revenus mensuels (12 derniers mois)
     * Syntaxe PostgreSQL : TO_CHAR au lieu de DATE_FORMAT
     */
    public function getMonthlyEarnings()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Order::whereHas('serviceOffer.service', function ($query) use ($user) {
            $query->where('freelance_id', $user->freelance->id);
        })
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw("
                TO_CHAR(created_at, 'YYYY-MM') as month,
                SUM(amount) as total_earnings,
                COUNT(*) as orders_count
            ")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Récupérer les contrats récents
     */
    public function getRecentContracts(int $limit = 5)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à ce dashboard.');
        }

        return Contract::where('freelance_id', $user->freelance->id)
            ->with(['project', 'client.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
