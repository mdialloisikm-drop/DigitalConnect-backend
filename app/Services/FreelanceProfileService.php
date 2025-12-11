<?php

namespace App\Services;

use App\Models\Freelance;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class FreelanceProfileService
{
    /**
     * Liste paginée des freelances avec filtres
     */
    public function getFreelances(array $filters = [], int $perPage = 10)
    {
        $query = Freelance::with([
            'user:id,full_name,avatar,email,city,country',
            'skills:id,name'
        ]);

        // Filtre par taux horaire (min et max)
        if (isset($filters['min_rate']) && $filters['min_rate'] > 0) {
            $query->where('hourly_rate', '>=', $filters['min_rate']);
        }

        if (isset($filters['max_rate']) && $filters['max_rate'] > 0) {
            $query->where('hourly_rate', '<=', $filters['max_rate']);
        }

        // Filtre par compétences (array de skill_ids)
        if (isset($filters['skills']) && is_array($filters['skills']) && count($filters['skills']) > 0) {
            $query->whereHas('skills', function ($q) use ($filters) {
                $q->whereIn('skills.id', $filters['skills']);
            });
        }

        // Filtre par ville
        if (isset($filters['city']) && !empty($filters['city'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('city', 'ILIKE', '%' . $filters['city'] . '%');
            });
        }

        // Filtre par pays
        if (isset($filters['country']) && !empty($filters['country'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('country', 'ILIKE', '%' . $filters['country'] . '%');
            });
        }

        // Filtre par disponibilité
        if (isset($filters['availability']) && !empty($filters['availability'])) {
            $query->where('availability', $filters['availability']);
        }

        // Filtre par années d'expérience (minimum)
        if (isset($filters['min_experience']) && $filters['min_experience'] > 0) {
            $query->where('experience_years', '>=', $filters['min_experience']);
        }

        // Ordre par défaut : taux horaire croissant
        $orderBy = $filters['order_by'] ?? 'hourly_rate';
        $orderDirection = $filters['order_direction'] ?? 'asc';

        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Récupérer le profil détaillé d'un freelance
     */
    public function getFreelanceProfile(string $id)
    {
        $freelance = Freelance::with([
            'user:id,full_name,avatar,email,phone,city,country,created_at',
            'skills:id,name'
        ])->findOrFail($id);

        // Compter le nombre de projets complétés
        $completedProjects = Contract::where('freelance_id', $id)
            ->where('status', 'completed')
            ->count();

        // Compter le nombre de projets actifs
        $activeProjects = Contract::where('freelance_id', $id)
            ->where('status', 'active')
            ->count();

        // Compter le total de projets
        $totalProjects = Contract::where('freelance_id', $id)
            ->whereIn('status', ['active', 'completed'])
            ->count();

        // Calculer le taux de réussite
        $successRate = $totalProjects > 0
            ? round(($completedProjects / $totalProjects) * 100, 2)
            : 0;

        // Récupérer les services du freelance
        $services = $freelance->services()
            ->where('status', 'published')
            ->with(['category:id,name', 'images'])
            ->limit(3)
            ->get();

        return [
            'freelance' => $freelance,
            'statistics' => [
                'completed_projects' => $completedProjects,
                'active_projects' => $activeProjects,
                'total_projects' => $totalProjects,
                'success_rate' => $successRate,
            ],
            'services' => $services,
        ];
    }

    /**
     * Récupérer les filtres disponibles (pour le frontend)
     */
    public function getAvailableFilters()
    {
        // Récupérer toutes les compétences
        $skills = DB::table('skills')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Récupérer les villes disponibles
        $cities = DB::table('users')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        // Récupérer les pays disponibles
        $countries = DB::table('users')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->pluck('country')
            ->sort()
            ->values();

        // Récupérer les taux min et max
        $rateRange = DB::table('freelances')
            ->whereNotNull('hourly_rate')
            ->selectRaw('MIN(hourly_rate) as min_rate, MAX(hourly_rate) as max_rate')
            ->first();

        return [
            'skills' => $skills,
            'cities' => $cities,
            'countries' => $countries,
            'rate_range' => [
                'min' => $rateRange->min_rate ?? 0,
                'max' => $rateRange->max_rate ?? 1000,
            ],
        ];
    }
}
