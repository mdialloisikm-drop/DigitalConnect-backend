<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Récupérer les statistiques du dashboard admin
     * GET /api/admin/dashboard/stats
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
     * Récupérer les utilisateurs récents
     * GET /api/admin/dashboard/recent-users?limit=10
     */
    public function getRecentUsers(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $users = $this->dashboardService->getRecentUsers($limit);
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les projets récents
     * GET /api/admin/dashboard/recent-projects?limit=10
     */
    public function getRecentProjects(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $projects = $this->dashboardService->getRecentProjects($limit);
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les services récents
     * GET /api/admin/dashboard/recent-services?limit=10
     */
    public function getRecentServices(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $services = $this->dashboardService->getRecentServices($limit);
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer la répartition des utilisateurs par type
     * GET /api/admin/dashboard/user-type-breakdown
     */
    public function getUserTypeBreakdown()
    {
        try {
            $breakdown = $this->dashboardService->getUserTypeBreakdown();
            return response()->json($breakdown, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer la répartition des projets par statut
     * GET /api/admin/dashboard/project-status-breakdown
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
     * Récupérer la répartition des services par statut
     * GET /api/admin/dashboard/service-status-breakdown
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
     * Récupérer les inscriptions mensuelles
     * GET /api/admin/dashboard/monthly-registrations
     */
    public function getMonthlyRegistrations()
    {
        try {
            $registrations = $this->dashboardService->getMonthlyRegistrations();
            return response()->json($registrations, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les projets mensuels
     * GET /api/admin/dashboard/monthly-projects
     */
    public function getMonthlyProjects()
    {
        try {
            $projects = $this->dashboardService->getMonthlyProjects();
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les catégories les plus populaires
     * GET /api/admin/dashboard/top-categories?limit=10
     */
    public function getTopCategories(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $categories = $this->dashboardService->getTopCategories($limit);
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer les compétences les plus demandées
     * GET /api/admin/dashboard/top-skills?limit=10
     */
    public function getTopSkills(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $skills = $this->dashboardService->getTopSkills($limit);
            return response()->json($skills, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Récupérer toutes les données du dashboard en une seule requête
     * GET /api/admin/dashboard/overview
     */
    public function getOverview(Request $request)
    {
        try {
            $overview = [
                'statistics' => $this->dashboardService->getStatistics(),
                'recent_users' => $this->dashboardService->getRecentUsers(10),
                'recent_projects' => $this->dashboardService->getRecentProjects(5),
                'recent_services' => $this->dashboardService->getRecentServices(5),
                'user_type_breakdown' => $this->dashboardService->getUserTypeBreakdown(),
                'project_status_breakdown' => $this->dashboardService->getProjectStatusBreakdown(),
                'service_status_breakdown' => $this->dashboardService->getServiceStatusBreakdown(),
                'monthly_registrations' => $this->dashboardService->getMonthlyRegistrations(),
                'monthly_projects' => $this->dashboardService->getMonthlyProjects(),
                'top_categories' => $this->dashboardService->getTopCategories(10),
                'top_skills' => $this->dashboardService->getTopSkills(10),
            ];

            return response()->json($overview, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
