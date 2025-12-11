<?php

namespace App\Http\Controllers;

use App\Services\FreelanceProfileService;
use Illuminate\Http\Request;

class FreelanceProfileController extends Controller
{
    protected $freelanceProfileService;

    public function __construct(FreelanceProfileService $freelanceProfileService)
    {
        $this->freelanceProfileService = $freelanceProfileService;
    }

    /**
     * Liste paginée des freelances avec filtres
     * GET /api/freelances?min_rate=10&max_rate=100&skills[]=1&skills[]=2&city=Dakar&per_page=10
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'min_rate' => $request->input('min_rate'),
                'max_rate' => $request->input('max_rate'),
                'skills' => $request->input('skills', []),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'availability' => $request->input('availability'),
                'min_experience' => $request->input('min_experience'),
                'order_by' => $request->input('order_by', 'hourly_rate'),
                'order_direction' => $request->input('order_direction', 'asc'),
            ];

            $perPage = min($request->input('per_page', 10), 50);

            $freelances = $this->freelanceProfileService->getFreelances($filters, $perPage);

            return response()->json($freelances, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupérer le profil détaillé d'un freelance
     * GET /api/freelances/{id}
     */
    public function show(string $id)
    {
        try {
            $profile = $this->freelanceProfileService->getFreelanceProfile($id);
            return response()->json($profile, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Récupérer les filtres disponibles
     * GET /api/freelances/filters/available
     */
    public function availableFilters()
    {
        try {
            $filters = $this->freelanceProfileService->getAvailableFilters();
            return response()->json($filters, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
