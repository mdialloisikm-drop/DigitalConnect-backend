<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function index()
    {
        return User::with(['freelance', 'client'])->get();
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Charger la relation appropriée
        if ($user->user_type === 'freelance') {
            $user->load('freelance');
        } elseif ($user->user_type === 'client') {
            $user->load('client');
        }

        return $user;
    }

    public function updateProfile(array $data)
    {
        $user = auth()->user();

        // Gérer l'avatar
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            if ($user->avatar) {
                //Storage::disk('avatars')->delete($user->avatar);
                Storage::disk('s3')->delete($user->avatar);
            }
            $data['avatar'] = $this->storeAvatar($data['avatar']);
        }

        // Mettre à jour les champs de base
        $userFields = collect($data)->only([
            'full_name', 'email', 'phone', 'city', 'country', 'avatar'
        ])->filter()->toArray();

        $user->update($userFields);

        // Mettre à jour les champs spécifiques au type d'utilisateur
        if ($user->user_type === 'freelance' && $user->freelance) {
            $freelanceData = collect($data)->only([
                'title', 'description', 'hourly_rate', 'experience_years', 'availability'
            ])->filter(function ($value, $key) {
                // Garder les valeurs null pour hourly_rate et experience_years
                return $value !== null && $value !== '';
            })->toArray();

            if (!empty($freelanceData)) {
                $user->freelance->update($freelanceData);
            }
        } elseif ($user->user_type === 'client' && $user->client) {
            $clientData = collect($data)->only([
                'company_name', 'company_description'
            ])->filter(function ($value) {
                return $value !== null && $value !== '';
            })->toArray();

            if (!empty($clientData)) {
                $user->client->update($clientData);
            }
        }

        // IMPORTANT : Recharger l'utilisateur avec ses relations
        $user->refresh();

        if ($user->user_type === 'freelance') {
            $user->load('freelance');
        } elseif ($user->user_type === 'client') {
            $user->load('client');
        }

        return $user;
    }

    public function changePassword(array $data)
    {
        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Mot de passe actuel incorrect');
        }

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        return ['message' => 'Mot de passe modifié avec succès'];
    }

    public function deleteAccount()
    {
        $user = auth()->user();

        if ($user->avatar) {
            //Storage::disk('avatars')->delete($user->avatar);
            Storage::disk('s3')->delete($user->avatar);
        }

        $user->tokens()->delete();
        $user->delete();

        return ['message' => 'Compte supprimé avec succès'];
    }

    public function updateStatus(string $id, array $data)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            throw new \Exception('Vous ne pouvez pas modifier votre propre statut');
        }

        $user->update(['status' => $data['status']]);

        // Recharger avec les relations
        $user->refresh();
        if ($user->user_type === 'freelance') {
            $user->load('freelance');
        } elseif ($user->user_type === 'client') {
            $user->load('client');
        }

        return $user;
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            throw new \Exception('Vous ne pouvez pas supprimer votre propre compte');
        }

        if ($user->avatar) {
            //Storage::disk('avatars')->delete($user->avatar);
            Storage::disk('s3')->delete($user->avatar);
        }

        $user->tokens()->delete();
        $user->delete();

        return ['message' => 'Utilisateur supprimé avec succès'];
    }

    private function storeAvatar(UploadedFile $file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //$file->storeAs('', $filename, 'avatars');
        // ✅ CHANGEMENT: 'avatars' -> 's3'
        $file->storeAs('avatars', $filename, 's3');
        return 'avatars/' . $filename;
    }

    public function getFreelances()
    {
        return User::with('freelance')
            ->where('user_type', 'freelance')
            ->where('status', 'active')
            ->get();
    }

    public function getClients()
    {
        return User::with('client')
            ->where('user_type', 'client')
            ->where('status', 'active')
            ->get();
    }
}
