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

    public function getStatistics()
    {
        $user = auth('api')->user();

        if (!$user->isAdmin()) {
            throw new \Exception('Seuls les administrateurs peuvent accéder à ce dashboard.');
        }

        $projectStats = Project::selectRaw("
                COUNT(*) as total_projects,
                SUM(CASE WHEN status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as active_projects
            ")
            ->first();

        $serviceStats = Service::selectRaw("
                COUNT(*) as total_services,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_services
            ")
            ->first();

        $userStats = User::selectRaw("
                COUNT(*) as total_users,
                SUM(CASE WHEN user_type = 'freelance' THEN 1 ELSE 0 END) as total_freelances,
                SUM(CASE WHEN user_type = 'client' THEN 1 ELSE 0 END) as total_clients
            ")
            ->first();

        $total_categories = Category::count();
        $total_skills = Skill::count();

        return [
            'total_projects' => (int) ($projectStats->total_projects ?? 0),
            'total_services' => (int) ($serviceStats->total_services ?? 0),
            'total_users' => (int) ($userStats->total_users ?? 0),
            'total_freelances' => (int) ($userStats->total_freelances ?? 0),
            'total_clients' => (int) ($userStats->total_clients ?? 0),
            'total_categories' => $total_categories,
            'total_skills' => $total_skills,
            'active_projects' => (int) ($projectStats->active_projects ?? 0),
            'published_services' => (int) ($serviceStats->published_services ?? 0),
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
        return Service::with(['freelance.user', 'category', 'images', 'offers'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer la répartition des utilisateurs par type
     */
    public function getUserTypeBreakdown()
    {
        return User::selectRaw("
                user_type,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
            ")
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


    public function getMonthlyRegistrations()
    {
        return User::selectRaw("
                TO_CHAR(created_at, 'YYYY-MM') as month,
                COUNT(*) as total_registrations,
                SUM(CASE WHEN user_type = 'freelance' THEN 1 ELSE 0 END) as freelances,
                SUM(CASE WHEN user_type = 'client' THEN 1 ELSE 0 END) as clients
            ")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }


    public function getMonthlyProjects()
    {
        return Project::selectRaw("
                TO_CHAR(created_at, 'YYYY-MM') as month,
                COUNT(*) as total_projects,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(budget) as total_budget
            ")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }


    public function getTopCategories(int $limit = 10)
    {
        return Category::withCount(['projects', 'services'])
            ->orderBy('projects_count', 'desc')
            ->limit($limit)
            ->get();
    }

    
    public function getTopSkills(int $limit = 10)
    {
        return Skill::withCount(['projects', 'freelances'])
            ->orderBy('projects_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
