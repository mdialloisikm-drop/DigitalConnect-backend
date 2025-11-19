<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            if (auth()->user()->user_type !== 'admin') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $users = $this->userService->index();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(string $id)
    {
        try {
            if (auth()->user()->user_type !== 'admin') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $user = $this->userService->show($id);
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $data = $request->validated();

            // Ajouter le fichier avatar s'il existe
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar');
            }

            $user = $this->userService->updateProfile($data);

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur updateProfile: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $result = $this->userService->changePassword($request->validated());
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteAccount()
    {
        try {
            $result = $this->userService->deleteAccount();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateStatus(UpdateUserStatusRequest $request, string $id)
    {
        try {
            $user = $this->userService->updateStatus($id, $request->validated());
            return response()->json([
                'message' => 'Statut mis à jour avec succès',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            if (auth()->user()->user_type !== 'admin') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $result = $this->userService->destroy($id);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getFreelances()
    {
        try {
            $freelances = $this->userService->getFreelances();
            return response()->json($freelances, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getClients()
    {
        try {
            $clients = $this->userService->getClients();
            return response()->json($clients, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
