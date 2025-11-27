<?php

namespace App\Http\Controllers;

use App\Services\FreelanceDashboardService;
use Illuminate\Http\Request;

class FreelanceDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(FreelanceDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Récupérer les statistiques du dashboard freelance
     * GET /api/freelance/dashboard/stats
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
     * Récupérer les services récents
     * GET /api/freelance/dashboard/recent-services?limit=5
     */
    public function getRecentServices(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $services = $this->dashboardService->getRecentServices($limit);
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les commandes récentes
     * GET /api/freelance/dashboard/recent-orders?limit=5
     */
    public function getRecentOrders(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $orders = $this->dashboardService->getRecentOrders($limit);
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les propositions récentes
     * GET /api/freelance/dashboard/recent-proposals?limit=5
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
     * Récupérer la répartition des services par statut
     * GET /api/freelance/dashboard/service-status-breakdown
     */
    public function getServiceStatusBreakdown()
    {
        try {
            $breakdown = $this->dashboardService->getServiceStatusBreakdown();
            return response()->json($breakdown, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer la répartition des commandes par statut
     * GET /api/freelance/dashboard/order-status-breakdown
     */
    public function getOrderStatusBreakdown()
    {
        try {
            $breakdown = $this->dashboardService->getOrderStatusBreakdown();
            return response()->json($breakdown, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les revenus mensuels
     * GET /api/freelance/dashboard/monthly-earnings
     */
    public function getMonthlyEarnings()
    {
        try {
            $earnings = $this->dashboardService->getMonthlyEarnings();
            return response()->json($earnings, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les contrats récents
     * GET /api/freelance/dashboard/recent-contracts?limit=5
     */
    public function getRecentContracts(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $contracts = $this->dashboardService->getRecentContracts($limit);
            return response()->json($contracts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer toutes les données du dashboard en une seule requête
     * GET /api/freelance/dashboard/overview
     */
    public function getOverview(Request $request)
    {
        try {
            $overview = [
                'statistics' => $this->dashboardService->getStatistics(),
                'recent_services' => $this->dashboardService->getRecentServices(5),
                'recent_orders' => $this->dashboardService->getRecentOrders(5),
                'recent_proposals' => $this->dashboardService->getRecentProposals(5),
                'recent_contracts' => $this->dashboardService->getRecentContracts(5),
                'service_status_breakdown' => $this->dashboardService->getServiceStatusBreakdown(),
                'order_status_breakdown' => $this->dashboardService->getOrderStatusBreakdown(),
                'monthly_earnings' => $this->dashboardService->getMonthlyEarnings(),
            ];

            return response()->json($overview, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
