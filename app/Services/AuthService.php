<?php

namespace App\Services;

use App\Models\User;
use App\Models\Freelance;
use App\Models\Client;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(array $data)
    {
        // Hash du mot de passe
        $userData = $data;
        $userData['password'] = Hash::make($data['password']);
        $userData['status'] = 'active';
        $userData['email_verified_at'] = null; // Email non vérifié par défaut

        // Retirer les champs qui ne sont pas dans la table users
        $userOnlyData = collect($userData)->only([
            'full_name', 'email', 'password', 'phone',
            'city', 'country', 'user_type', 'status', 'email_verified_at'
        ])->toArray();

        // Gérer l'avatar si présent
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $userOnlyData['avatar'] = $this->storeImage($data['avatar']);
        }

        $user = User::create($userOnlyData);

        // Créer le profil spécifique selon le type d'utilisateur
        if ($data['user_type'] === 'freelance') {
            Freelance::create([
                'user_id' => $user->id,
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'availability' => 'available',
            ]);
        } elseif ($data['user_type'] === 'client') {
            Client::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'] ?? null,
                'company_description' => $data['company_description'] ?? null,
            ]);
        }

        // Générer et envoyer le token de vérification
        $token = $user->generateEmailVerificationToken();
        $user->notify(new VerifyEmailNotification($token));

        // Charger la relation seulement si ce n'est pas un admin
        if ($user->user_type !== 'admin') {
            $user->load($user->user_type);
        }

        return [
            'user' => $user,
            'message' => 'Inscription réussie ! Un email de vérification a été envoyé à votre adresse email.'
        ];
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(array $data)
    {
        if (!$token = auth('api')->attempt($data)) {
            throw new \Exception('Identifiants invalides');
        }

        $user = auth('api')->user();

        // Vérifier si l'email est vérifié
        if (!$user->hasVerifiedEmail()) {
            auth('api')->logout();
            throw new \Exception('Veuillez vérifier votre adresse email avant de vous connecter. Vérifiez vos emails.');
        }

        if ($user->status !== 'active') {
            auth('api')->logout();
            throw new \Exception('Compte suspendu ou inactif');
        }

        // Charger la relation seulement si ce n'est pas un admin
        if ($user->user_type !== 'admin') {
            $user->load($user->user_type);
        }

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    /**
     * Vérifier l'email avec le token
     */
    public function verifyEmail(string $email, string $token)
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            throw new \Exception('Cet email a déjà été vérifié.');
        }

        $hashedToken = hash('sha256', $token);

        if ($user->email_verification_token !== $hashedToken) {
            throw new \Exception('Token de vérification invalide.');
        }

        if ($user->email_verification_token_expires_at < now()) {
            throw new \Exception('Le token de vérification a expiré. Veuillez demander un nouveau lien.');
        }

        $user->markEmailAsVerified();

        return [
            'message' => 'Email vérifié avec succès ! Vous pouvez maintenant vous connecter.'
        ];
    }

    /**
     * Renvoyer l'email de vérification
     */
    public function resendVerificationEmail(string $email)
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            throw new \Exception('Cet email a déjà été vérifié.');
        }

        $token = $user->generateEmailVerificationToken();
        $user->notify(new VerifyEmailNotification($token));

        return [
            'message' => 'Un nouvel email de vérification a été envoyé.'
        ];
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        auth('api')->logout();
        return ['message' => 'Déconnexion réussie'];
    }

    /**
     * Récupérer le profil de l'utilisateur connecté
     */
    public function me()
    {
        $user = auth('api')->user();

        // Charger la relation seulement si ce n'est pas un admin
        if ($user->user_type !== 'admin') {
            $user->load($user->user_type);
        }

        return $user;
    }

    /**
     * Stocker l'image avatar
     */
    private function storeImage(UploadedFile $file): string
    {
        // Générer un nom unique pour l'image
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Stocker l'image dans le disk 'avatars'
        //$file->storeAs('', $filename, 'avatars');
        // ✅ CHANGEMENT:  'avatars' -> 's3'
        $file->storeAs('avatars', $filename, 's3');

        return 'avatars/' . $filename;
    }
}
