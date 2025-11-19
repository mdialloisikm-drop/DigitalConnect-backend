<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Service;
use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Récupérer les statistiques du dashboard admin
     * VERSION OPTIMISÉE : Requêtes SQL optimisées au lieu de charger toutes les données
     */
    public function getStatistics()
    {
        $user = auth('api')->user();

        if (!$user->isAdmin()) {
            throw new \Exception('Seuls les administrateurs peuvent accéder à ce dashboard.');
        }

        // ✅ OPTIMISATION 1 : Compter les projets et les projets actifs (1 seule requête)
        $projectStats = Project::selectRaw('
                COUNT(*) as total_projects,
                SUM(CASE WHEN status IN ("open", "in_progress") THEN 1 ELSE 0 END) as active_projects
            ')
            ->first();

        // ✅ OPTIMISATION 2 : Compter les services et les services publiés (1 seule requête)
        $serviceStats = Service::selectRaw('
                COUNT(*) as total_services,
                SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published_services
            ')
            ->first();

        // ✅ OPTIMISATION 3 : Compter les utilisateurs par type (1 seule requête)
        $userStats = User::selectRaw('
                COUNT(*) as total_users,
                SUM(CASE WHEN user_type = "freelance" THEN 1 ELSE 0 END) as total_freelances,
                SUM(CASE WHEN user_type = "client" THEN 1 ELSE 0 END) as total_clients
            ')
            ->first();

        // ✅ OPTIMISATION 4 : Compter les catégories et les compétences (2 requêtes simples)
        $total_categories = Category::count();
        $total_skills = Skill::count();

        // ✅ Retourner les statistiques
        return [
            'total_projects' => $projectStats->total_projects ?? 0,
            'total_services' => $serviceStats->total_services ?? 0,
            'total_users' => $userStats->total_users ?? 0,
            'total_freelances' => $userStats->total_freelances ?? 0,
            'total_clients' => $userStats->total_clients ?? 0,
            'total_categories' => $total_categories,
            'total_skills' => $total_skills,
            'active_projects' => $projectStats->active_projects ?? 0,
            'published_services' => $serviceStats->published_services ?? 0,
        ];
    }

    /**
     * Récupérer les utilisateurs récents
     */
    public function getRecentUsers(int $limit = 10)
    {
        return User::with(['freelance', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les projets récents
     */
    public function getRecentProjects(int $limit = 10)
    {
        return Project::with(['client.user', 'category', 'skills'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les services récents
     */
    public function getRecentServices(int $limit = 10)
    {
        return Service::with(['freelance.user', 'category', 'skills'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer la répartition des utilisateurs par type
     */
    public function getUserTypeBreakdown()
    {
        return User::selectRaw('
                user_type,
                COUNT(*) as count,
                SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_count
            ')
            ->groupBy('user_type')
            ->get()
            ->keyBy('user_type');
    }

    /**
     * Récupérer la répartition des projets par statut
     */
    public function getProjectStatusBreakdown()
    {
        return Project::selectRaw('
                status,
                COUNT(*) as count,
                SUM(budget) as total_budget
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Récupérer la répartition des services par statut
     */
    public function getServiceStatusBreakdown()
    {
        return Service::selectRaw('
                status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Récupérer les statistiques d'inscription (12 derniers mois)
     */
    public function getMonthlyRegistrations()
    {
        return User::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total_registrations,
                SUM(CASE WHEN user_type = "freelance" THEN 1 ELSE 0 END) as freelances,
                SUM(CASE WHEN user_type = "client" THEN 1 ELSE 0 END) as clients
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Récupérer les statistiques de projets par mois (12 derniers mois)
     */
    public function getMonthlyProjects()
    {
        return Project::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total_projects,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(budget) as total_budget
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Récupérer les catégories les plus populaires
     */
    public function getTopCategories(int $limit = 10)
    {
        return Category::withCount(['projects', 'services'])
            ->orderBy('projects_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les compétences les plus demandées
     */
    public function getTopSkills(int $limit = 10)
    {
        return Skill::withCount(['projects', 'services'])
            ->orderBy('projects_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
