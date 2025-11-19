<?php

namespace App\Http\Controllers;

use App\Services\ClientDashboardService;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(ClientDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Récupérer les statistiques du dashboard client
     * GET /api/client/dashboard/stats
     */
    public function getStatistics()
    {
        try {
            $statistics = $this->dashboardService->getStatistics();
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les projets récents
     * GET /api/client/dashboard/recent-projects?limit=5
     */
    public function getRecentProjects(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $projects = $this->dashboardService->getRecentProjects($limit);
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les propositions récentes
     * GET /api/client/dashboard/recent-proposals?limit=5
     */
    public function getRecentProposals(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $proposals = $this->dashboardService->getRecentProposals($limit);
            return response()->json($proposals, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer le détail des statuts de projets
     * GET /api/client/dashboard/project-status-breakdown
     */
    public function getProjectStatusBreakdown()
    {
        try {
            $breakdown = $this->dashboardService->getProjectStatusBreakdown();
            return response()->json($breakdown, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les dépenses mensuelles
     * GET /api/client/dashboard/monthly-spending
     */
    public function getMonthlySpending()
    {
        try {
            $spending = $this->dashboardService->getMonthlySpending();
            return response()->json($spending, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer toutes les données du dashboard en une seule requête
     * GET /api/client/dashboard/overview
     */
    public function getOverview(Request $request)
    {
        try {
            $overview = [
                'statistics' => $this->dashboardService->getStatistics(),
                'recent_projects' => $this->dashboardService->getRecentProjects(5),
                'recent_proposals' => $this->dashboardService->getRecentProposals(5),
                'project_status_breakdown' => $this->dashboardService->getProjectStatusBreakdown(),
                'monthly_spending' => $this->dashboardService->getMonthlySpending(),
            ];

            return response()->json($overview, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
